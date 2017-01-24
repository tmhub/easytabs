document.observe('dom:loaded', function(){

    // listen click on write review link
    $$('.rating-links a, .no-rating a').each(function(el) {
        el.observe('click', function(event) {
            var writeReview = (el.href.indexOf('#review-form') > -1);
            if (writeReview && !$('review-form')) {
                return;
            }
            event.stop();
            easytabs.each(function (tabs){
                var reviewForm = tabs.container.down('#review-form');
                if (!reviewForm) { return; }
                var tabContent = reviewForm.up('.easytabs-content');
                if (!tabContent) { return; }
                var alias = tabContent.readAttribute('data-tab-alias');
                window.location.hash =
                    tabs.tpl.href.replace(tabs.tpl.tab, alias);
                tabs.onclick(el, event, alias, true);
            });
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
