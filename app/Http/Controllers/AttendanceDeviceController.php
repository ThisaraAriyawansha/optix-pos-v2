<?php

namespace App\Http\Controllers;

use App\Support\AttendanceClock;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Fingerprint devices. Each person is enrolled on the device under their employee
 * number (EMP-0012 → 12). Every punch toggles check-in / check-out.
 */
class AttendanceDeviceController extends Controller
{
    public function __construct(protected AttendanceClock $clock)
    {
    }

    /**
     * Generic endpoint for any device or bridge app:
     * POST /attendance/device/punch  {pin, punched_at?, device_sn?}  header X-Device-Token.
     */
    public function punch(Request $request)
    {
        $token = (string) config('attendance.device_token');
        abort_unless($token !== '' && hash_equals($token, (string) ($request->header('X-Device-Token') ?? $request->input('token'))), 403);

        $validated = $request->validate([
            'pin' => ['required', 'string', 'max:30'],
            'punched_at' => ['nullable', 'date'],
            'device_sn' => ['nullable', 'string', 'max:50'],
        ]);

        $punch = $this->clock->recordPunch(
            $validated['device_sn'] ?? 'api',
            $validated['pin'],
            isset($validated['punched_at']) ? Carbon::parse($validated['punched_at']) : now(),
        );

        $person = $punch->attendance?->attendable;

        return response()->json([
            'result' => $punch->result,
            'name' => $person?->name,
            'time' => $punch->punched_at->toDateTimeString(),
        ], $punch->result === 'unknown' ? 404 : 200);
    }

    /**
     * ZKTeco "ADMS / push" handshake: GET /iclock/cdata?SN=…
     * Tells the device to send attendance logs in real time.
     */
    public function handshake(Request $request)
    {
        $sn = $this->allowedSerial($request);

        $offset = now()->utcOffset() / 60;

        return $this->plain(implode("\n", [
            "GET OPTION FROM: {$sn}",
            'ATTLOGStamp=None',
            'OPERLOGStamp=9999',
            'ATTPHOTOStamp=None',
            'ErrorDelay=30',
            'Delay=10',
            'TransTimes=00:00;14:05',
            'TransInterval=1',
            'TransFlag=TransData AttLog',
            'TimeZone='.$offset,
            'Realtime=1',
            'Encrypt=None',
        ]));
    }

    /**
     * ZKTeco upload: POST /iclock/cdata?SN=…&table=ATTLOG
     * Body lines: PIN \t YYYY-MM-DD HH:MM:SS \t status \t verify …
     */
    public function receive(Request $request)
    {
        $sn = $this->allowedSerial($request);

        if (strtoupper((string) $request->query('table')) !== 'ATTLOG') {
            return $this->plain('OK');
        }

        $lines = collect(preg_split('/\r\n|\r|\n/', trim($request->getContent())))
            ->map(fn ($line) => preg_split('/\t/', trim($line)))
            ->filter(fn ($cols) => count($cols) >= 2 && $cols[0] !== '' && strtotime($cols[1]) !== false)
            ->sortBy(fn ($cols) => $cols[1]);

        foreach ($lines as $cols) {
            try {
                $this->clock->recordPunch($sn, $cols[0], Carbon::parse($cols[1]));
            } catch (\Throwable $e) {
                Log::warning('Attendance punch failed', ['sn' => $sn, 'line' => $cols, 'error' => $e->getMessage()]);
            }
        }

        return $this->plain('OK: '.$lines->count());
    }

    /** ZKTeco polls for commands: GET /iclock/getrequest — we never send any. */
    public function getRequest(Request $request)
    {
        $this->allowedSerial($request);

        return $this->plain('OK');
    }

    protected function allowedSerial(Request $request): string
    {
        $sn = (string) $request->query('SN');
        abort_unless($sn !== '' && in_array($sn, config('attendance.device_serials'), true), 403);

        return $sn;
    }

    protected function plain(string $body)
    {
        return response($body, 200)->header('Content-Type', 'text/plain');
    }
}
