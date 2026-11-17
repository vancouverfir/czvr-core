window.startDashboardTour = function startDashboardTour() {
    if (!window.driver?.js?.driver) {
        return;
    }

    const reveal = (element) => {
        let section = element.closest('details:not([open])');

        while (section) {
            section.open = true;
            section = section.parentElement?.closest('details:not([open])');
        }
    };

    const steps = Array.from(document.querySelectorAll('[data-step][data-intro]'))
        .sort((left, right) => Number(left.dataset.step) - Number(right.dataset.step))
        .map((element) => ({
            element,
            onHighlightStarted: () => reveal(element),
            popover: {
                description: element.dataset.intro,
                side: 'bottom',
                align: 'start',
            },
        }));

    window.driver.js.driver({
        allowClose: true,
        animate: true,
        showProgress: true,
        steps,
    }).drive();
};
