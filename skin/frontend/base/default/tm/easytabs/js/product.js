document.observe('dom:loaded', function(){

    // listen click on write review link
    $$('.rating-links a, .no-rating a').each(function(el) {
        el.observe('click', function(event) {
            var stopEventFlag = false;
            easytabs.each(function (tabs){
                var reviewForm = tabs.container.down('#review-form');
                if (!reviewForm) { return; }
                var tabContent = reviewForm.up('.easytabs-content');
                if (!tabContent) { return; }
                var alias = tabContent.readAttribute('data-tab-alias');
                stopEventFlag = true;
                window.location.hash =
                    tabs.tpl.href.replace(tabs.tpl.tab, alias);
                tabs.onclick(el, event, alias, false);
                // scroll to form itself so customer could post review right away
                Effect.ScrollTo(reviewForm.up('.form-add'), {
                    duration: el.hasClassName('easytabs-animate')
                        ? tabs.config.scrollSpeed
                        : 0,
                    offset: $$('.header-container[sticky_kit]').first()
                        ? -$$('.header-container[sticky_kit]').first().getHeight()
                        : 0
                });
            });
            if (stopEventFlag) {
                event.stop();
            }
        });
    });

    // add tag form initialization
    $$('.easytabs-content #addTagForm').each(function (form){
        if (typeof window.submitTagForm === 'undefined') {
            window.addTagFormJs = new VarienForm(form);
            window.submitTagForm = function () {
                if(addTagFormJs.validator.validate()) {
                    addTagFormJs.form.submit();
                }
            };
        } else {
            throw $break;
        }
    });

});
