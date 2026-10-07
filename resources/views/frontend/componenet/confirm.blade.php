{{--
    Global confirm / alert dialog (replaces the browser's native confirm()).

    Usage on any form:
        <form ... data-confirm="Delete this?" data-confirm-variant="danger"
                  data-confirm-title="Delete" data-confirm-ok="Delete">
    From JS:
        appConfirm('Message', { title, ok, cancel, variant }).then(yes => ...)
        appAlert('Message', { title, ok })
--}}
<div id="app-confirm" class="fixed inset-0 z-[100] hidden items-end sm:items-center justify-center p-4"
     role="dialog" aria-modal="true" aria-labelledby="app-confirm-title" aria-describedby="app-confirm-message">
    <div data-confirm-backdrop class="absolute inset-0 bg-black/40 backdrop-blur-[2px] opacity-0 transition-opacity duration-150"></div>

    <div data-confirm-panel
         class="relative w-full max-w-sm bg-surface rounded-2xl shadow-xl border border-subtle p-5 sm:p-6
                translate-y-4 sm:translate-y-0 sm:scale-95 opacity-0 transition-all duration-150">
        <div class="flex items-start gap-3">
            <div data-confirm-icon class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center"></div>
            <div class="min-w-0 flex-1 pt-0.5">
                <h2 id="app-confirm-title" class="font-heading text-base font-semibold text-gray-900 dark:text-white"></h2>
                <p id="app-confirm-message" class="mt-1 text-sm text-gray-600 dark:text-gray-300 font-sans break-words"></p>
            </div>
        </div>

        <div class="mt-5 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
            <button type="button" data-confirm-cancel
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-sm font-semibold border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200 active:scale-95 transition-transform"></button>
            <button type="button" data-confirm-ok
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-sm font-semibold text-white active:scale-95 transition-transform"></button>
        </div>
    </div>
</div>

<script>
    (function () {
        const root = document.getElementById('app-confirm');
        const backdrop = root.querySelector('[data-confirm-backdrop]');
        const panel = root.querySelector('[data-confirm-panel]');
        const icon = root.querySelector('[data-confirm-icon]');
        const titleEl = root.querySelector('#app-confirm-title');
        const messageEl = root.querySelector('#app-confirm-message');
        const okBtn = root.querySelector('[data-confirm-ok]');
        const cancelBtn = root.querySelector('[data-confirm-cancel]');

        const text = {
            confirmTitle: @js(__('Are you sure?')),
            alertTitle: @js(__('Notice')),
            ok: @js(__('Confirm')),
            alertOk: @js(__('OK')),
            cancel: @js(__('Cancel')),
        };

        const icons = {
            danger: '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>',
            primary: '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8h.01M11 12h1v4h1"/></svg>',
        };

        let resolver = null;
        let lastFocus = null;

        function open(message, opts, isAlert) {
            const variant = opts.variant === 'danger' ? 'danger' : 'primary';
            titleEl.textContent = opts.title || (isAlert ? text.alertTitle : text.confirmTitle);
            messageEl.textContent = message || '';
            okBtn.textContent = opts.ok || (isAlert ? text.alertOk : text.ok);
            cancelBtn.textContent = opts.cancel || text.cancel;
            cancelBtn.classList.toggle('hidden', isAlert);

            okBtn.classList.toggle('bg-red-600', variant === 'danger');
            okBtn.classList.toggle('bg-brand', variant !== 'danger');
            icon.className = 'shrink-0 w-10 h-10 rounded-full flex items-center justify-center ' + (variant === 'danger'
                ? 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400'
                : 'bg-blue-50 text-brand dark:bg-blue-900/30');
            icon.innerHTML = icons[variant];

            lastFocus = document.activeElement;
            root.classList.remove('hidden');
            root.classList.add('flex');
            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('opacity-0', 'translate-y-4', 'sm:scale-95');
            });
            (isAlert ? okBtn : cancelBtn).focus();

            return new Promise((resolve) => { resolver = resolve; });
        }

        function close(result) {
            if (!resolver) return;
            backdrop.classList.add('opacity-0');
            panel.classList.add('opacity-0', 'translate-y-4', 'sm:scale-95');
            setTimeout(() => {
                root.classList.add('hidden');
                root.classList.remove('flex');
            }, 150);
            const resolve = resolver;
            resolver = null;
            if (lastFocus && lastFocus.focus) lastFocus.focus();
            resolve(result);
        }

        okBtn.addEventListener('click', () => close(true));
        cancelBtn.addEventListener('click', () => close(false));
        backdrop.addEventListener('click', () => close(false));
        document.addEventListener('keydown', (e) => {
            if (!resolver) return;
            if (e.key === 'Escape') { e.preventDefault(); close(false); }
            if (e.key === 'Tab') {
                // keep focus inside the dialog
                const focusable = [cancelBtn, okBtn].filter((b) => !b.classList.contains('hidden'));
                const i = focusable.indexOf(document.activeElement);
                e.preventDefault();
                focusable[(i + (e.shiftKey ? -1 : 1) + focusable.length) % focusable.length].focus();
            }
        });

        window.appConfirm = (message, opts = {}) => open(message, opts, false);
        window.appAlert = (message, opts = {}) => open(message, opts, true);

        // Any <form data-confirm="..."> asks before submitting (runs after HTML validation).
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (!form.matches('form[data-confirm]')) return;
            if (form.dataset.confirmed === '1') { delete form.dataset.confirmed; return; }

            e.preventDefault();
            const submitter = e.submitter || null;
            window.appConfirm(form.dataset.confirm, {
                title: form.dataset.confirmTitle,
                ok: form.dataset.confirmOk,
                cancel: form.dataset.confirmCancel,
                variant: form.dataset.confirmVariant,
            }).then((yes) => {
                if (!yes) return;
                form.dataset.confirmed = '1';
                form.requestSubmit ? form.requestSubmit(submitter) : form.submit();
            });
        }, true);
    })();
</script>
