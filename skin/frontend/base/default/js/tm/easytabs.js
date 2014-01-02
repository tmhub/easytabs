var EasyTabs = Class.create();
EasyTabs.prototype = {
    initialize: function(selector) {
        $$(selector).each(this.initTab.bind(this));
        this.showContent(window.location.hash);
        var contents = $$(window.location.hash + '_contents').first();
        if (contents) {
            contents.scrollTo();
        }
    },

    initTab: function(el) {
        if ($(el).up('li') && $(el).up('li').hasClassName('active')) {
            this.showContent(el);
        }
        el.observe('click', this.showContent.bind(this, el));
    },

    showContent: function(a) {
        if ('string' == typeof a) {
            var hash = a.split("#")[1];
        } else {
            var hash = a.href.split("#")[1];
        }
        if (!hash) {
            return this;
        }

        var contentToShow = $(hash + '_contents');
        if (!contentToShow) {
            return;
        }
        var easytabContainer = contentToShow.up('.padder').previous('.easytabs');
        contentToShow.siblings().each(function(contentToHide) {
            contentToHide.hide();
            var _hash = contentToHide.id.replace('_contents', ''),
                a = easytabContainer.select('a[href="#' + _hash + '"]').first();
            if (a) {
                a.up('li').removeClassName('active');
            }
        });
        contentToShow.show();
        a = easytabContainer.select('a[href="#' + hash + '"]').first();
        if (a) {
            a.up('li').addClassName('active');
        }
        return this;
    }
};
