var PopupDialog = (function () {
    // ── Cookie helpers ──────────────────────────────────────────
    function setCookie(name, value, days) {
        var expires = '';
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + (value || '') + expires + '; path=/';
    }

    function getCookie(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    function init(config) {
        config = config || {};
        var dialogId = config.dialogId;
        var cookieName = config.cookieName;
        var cookieDays = config.cookieDays !== undefined ? config.cookieDays : 30;

        if (!dialogId || !cookieName) {
            console.error('PopupDialog: missing dialogId or cookieName');
            return;
        }

        var dialog = document.getElementById(dialogId);
        if (!dialog) return;
        if (cookieDays > 0 && getCookie(cookieName)) return;

        var trigger = dialog.getAttribute('data-trigger');
        var close_overlay_click = dialog.getAttribute('data-close-overlay-click') == '1';
        var pageLoadDelay = parseInt(dialog.getAttribute('data-page-load-delay'), 10) || 0;
        var scrollThreshold = parseInt(dialog.getAttribute('data-scroll-threshold'), 10) || 0;
        var closing = false;

        function openDialogWithAnimation() {
            dialog.showModal();
            dialog.offsetHeight; // force reflow
            dialog.classList.add('in');
        }

        function handleClose() {
            dialog.classList.remove('in', 'out');
            dialog.close();
            if (cookieDays > 0) setCookie(cookieName, '1', cookieDays);
            closing = false;
        }

        function closeDialogWithAnimation() {
            if (!dialog.open || closing) return;
            closing = true;

            dialog.classList.add('out');

            dialog.addEventListener('animationend', handleClose, { once: true });

            // Safety-net - force-close if animationend never fires.
            setTimeout(function () {
                if (!closing) return;
                handleClose();
            }, 400);
        }

        // ── Triggers ─────────────────────────────────────────────────
        if (trigger === 'page_load') {
            setTimeout(function () {
                if (cookieDays === 0 || !getCookie(cookieName)) openDialogWithAnimation();
            }, pageLoadDelay);
        }
        else if (trigger === 'exit_intent') {
            var triggered = false;
            window.addEventListener('mouseout', function (e) {
                if (!triggered && e.clientY <= 0) {
                    triggered = true;
                    openDialogWithAnimation();
                }
            });
        }
        else if (trigger === 'scroll') {
            var triggered = false;
            if (window.scrollY >= scrollThreshold) {
                triggered = true;
                openDialogWithAnimation();
            }
            if (!triggered) {
                var sentinel = document.createElement('div');
                sentinel.style.cssText = 'position:absolute;top:' + scrollThreshold +
                    'px;height:1px;width:1px;pointer-events:none;';
                document.body.appendChild(sentinel);
                var observer = new IntersectionObserver(function (entries) {
                    if (!triggered && entries[0].isIntersecting) {
                        triggered = true;
                        observer.disconnect();
                        sentinel.remove();
                        openDialogWithAnimation();
                    }
                }, { threshold: 0 });
                observer.observe(sentinel);
            }
        }

        var closeForm = dialog.querySelector('form');
        if (closeForm) {
            closeForm.addEventListener('submit', function (e) {
                e.preventDefault();
                closeDialogWithAnimation();
            });
        }

        dialog.addEventListener('cancel', function (e) {
            e.preventDefault();
            closeDialogWithAnimation();
        });

        if (close_overlay_click) {
            dialog.addEventListener('click', function (e) {
                var rect = dialog.getBoundingClientRect();
                var clickedInside = (
                    e.clientX >= rect.left &&
                    e.clientX <= rect.right &&
                    e.clientY >= rect.top &&
                    e.clientY <= rect.bottom
                );
                if (!clickedInside) closeDialogWithAnimation();
            });
        }
    }

    return { init: init };
})();
