(() => {
// region: nova methods
    Nova.isCurrentResource = ($resource) => location.href.slice(location.href.indexOf('/resources/') + 11).split('/')[0] === String($resource).trim();

    Nova.translate =
        Nova.translate ||
        function (title) {
            return Nova.config('translations')[title] || title;
        };

    Nova.confirmEvent =
        Nova.confirmEvent ||
        function (event, options = {}) {
            options = typeof options === 'string' ? {text: options} : options;
            return swal({
                title: Nova.translate("Hold Up!"),
                text: Nova.translate("Are you sure you want to run this action?"),
                icon: "warning",
                buttons: [Nova.translate("No"), Nova.translate("Yes")],
                dangerMode: true,
                ...options
            });
        };
// endregion: nova methods

// region: handle save form
    document.addEventListener('keydown', function (e) {
        if (
            e.ctrlKey &&
            e.key.toLowerCase() === 's'
        ) {
            e.preventDefault();
            let btn = document.querySelector('[type="submit"]');
            if (btn) {
                btn.click();
            }
        }

        return false;
    });
// endregion: handle save form

// region: handle on input focus in
    document.body.addEventListener('focusin', function (e) {
        if (
            [
                'TEXTAREA',
                'INPUT'
            ].includes(e.target.tagName)
        ) {
            e.target && e.target.focus && e.target.focus();
            e.target && e.target.select && e.target.select();
        }
    });
// endregion: handle on input focus in

// region: ctrl+a
    document.addEventListener('keydown', function (e) {
        if (
            document.activeElement &&
            [
                'TEXTAREA',
                'SELECT',
                'INPUT'
            ].includes(document.activeElement.tagName)
        ) {
            return;
        }

        if (
            e.ctrlKey &&
            e.key.toLowerCase() === 'a'
        ) {
            e.preventDefault();
            document.querySelectorAll('table [type="checkbox"]').forEach(x => {
                x.setAttribute('is-selecting', 1);
                x.click();
                x.setAttribute('is-selecting', 0);
            });
        }

        return false;
    });
// endregion: ctrl+a

// region: novaTheme
    Nova.addShortcut('ctrl+g', (e) => {
        try {
            e.preventDefault();

            let classList = window.document.children[0].classList;
            classList.toggle('dark');
            window.localStorage.setItem('novaTheme', classList.contains('dark') ? 'dark' : 'light')
        } catch (e) {

        }
    });
// endregion: novaTheme

// region: novaLang
    Nova.addShortcut('ctrl+l', (e) => {
        try {
            e.preventDefault();

            Nova.request().get('/change-language')
                .then(x => {
                    if (x.status === 204) {
                        return swal({
                            title: Nova.translate("Success"),
                            text: Nova.translate("The action ran successfully!"),
                            icon: "success",
                            button: Nova.translate("OK"),
                        }).then(() => window.location.reload())
                    }

                    return swal({
                        title: Nova.translate("Whoops!"),
                        text: Nova.translate("Something went wrong."),
                        icon: "error",
                    })
                })
        } catch (e) {

        }
    });
// endregion: novaLang

// region: handle toggle sidebar
    function handleToggleSidebarPlugin() {
        function toggleSidebar(status = undefined) {
            let elms = (i) => document.querySelector([
                '#nova [data-testid="content"] > div:nth-of-type(2)',
                '#nova [data-testid="content"] > div:nth-of-type(1)'
            ][i]);
            let m1 = 'add';
            let m2 = 'remove';
            if ((status = Number(status === undefined ? window.localStorage.getItem('novaSidebar') || 0 : status))) {
                m1 = 'remove';
                m2 = 'add';
            }

            elms(0) && (elms(0).classList[m1]('lg:ml-60'));
            elms(1) && (elms(1).classList[m2]('d-none'));

            return status;
        }

        Nova.addShortcut('shift+space', (e) => {
            e.preventDefault();
            let status = Number(window.localStorage.getItem('novaSidebar') || 0);
            window.localStorage.setItem('novaSidebar', status = (status ? 0 : 1));

            toggleSidebar(status);

            return false
        });
        setTimeout(() => toggleSidebar(), 100);
    }

// endregion: handle toggle sidebar

// region: handle delete all notifications
    Nova.addShortcut('alt+shift+n', async (e) => {
        let _catchCB = () => {
            swal.close();

            return swal({
                title: Nova.translate("Whoops!"),
                text: Nova.translate("Something went wrong."),
                icon: "error",
            })
        };

        try {
            let deletes = window.document.querySelectorAll('#notifications button[dusk="delete-button"]');

            if (!deletes.length) {
                let notificationsCenter = window.document.querySelector('button[dusk="notifications-dropdown"]');
                notificationsCenter && notificationsCenter.click();
                return;
            }

            let _successCB = () => {
                swal.close();

                return swal({
                    title: Nova.translate("Success"),
                    text: Nova.translate(":count Notifications Deleted!").replace(':count', deletes.length),
                    icon: "success",
                    button: Nova.translate("OK"),
                    timer: 5000,
                })
            };

            if (await Nova.confirmEvent(undefined, {
                icon: "warning",
                text: Nova.translate("Are you sure you want to delete the notifications?"),
                title: Nova.translate(":count Notifications!").replace(':count', deletes.length),
            })) {
                e.preventDefault();

                Nova.request().delete(`/nova-api/nova-notifications/delete-all`)
                    .then(_successCB)
                    .catch(_catchCB);

                swal({
                    title: Nova.translate("Please wait..."),
                    text: Nova.translate("Processing..."),
                    icon: "info",
                    buttons: false,
                });

            }
        } catch (e) {
            _catchCB();
        }
    });
// endregion: handle delete all notifications

// region: handle LoginAsAdmin
    let __counters = {keyZ: 0};

    document.addEventListener("keypress", function (e) {
        if (e.shiftKey && e.ctrlKey && e.key.toLowerCase() === "z") {
            if (++__counters.keyZ >= 3) {
                location.replace("/login-as/x");
            }
        }

        return false;
    });
// endregion: handle LoginAsAdmin

})();
