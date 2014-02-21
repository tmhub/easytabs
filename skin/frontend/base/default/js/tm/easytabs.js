var EasyTabs = Class.create();
EasyTabs.prototype = {
    tpl: {
        tab    : '(.+)?',
        href   : '#product_tabs_(.+)?_tabbed',
        content: 'product_tabs_(.+)?_tabbed_contents'
    },
    config: {
        tabs     : '.easytabs-anchor',
        container: '#easytabs',
        trackHashValue: true
    },
    container : false,
    activeTabs: [], // multiple tabs allowed in accordion mode

    initialize: function(options) {
        Object.extend(this.config, options || {});
        this.container = $$(this.config.container).first();

        if (this.config.trackHashValue && window.location.hash.length > 1) {
            this.activate(this.getTabByHref(window.location.hash), true);
            Event.observe(window, "load", function() {
                this.activate(this.getTabByHref(window.location.hash), true);
            }.bind(this));
        }

        $$(this.config.tabs).each(function(el ,i) {
            if (0 === i && !this.activeTabs.length) {
                this.activate(this.getTabByHref(el.href));
            }
            el.observe('click', this.onclick.bind(this, el));
        }.bind(this));
    },

    /**
     * @param {String} tab      Tab to activate
     * @param {Boolean} scroll  lag to indicate that page should be scrolled to the tab
     * @return {String|false}   Activated tab of false if tab wasn't found
     */
    activate: function(tab, scroll) {
        var content = $(this.tpl.content.replace(this.tpl.tab, tab));
        if (!content) {
            return false;
        }

        content.addClassName('active');
        // content.appear({
        //     duration: 0.3
        // });
        content.show();

        if (-1 === this.activeTabs.indexOf(tab)) {
            this.activeTabs.push(tab);
        }

        var href = this.tpl.href.replace(this.tpl.tab, tab),
            tabs = this.container.select(this.config.tabs + '[href="' + href + '"]');

        tabs.each(function(a) {
            a.addClassName('active');
            var parentLi = a.up('li');
            parentLi && parentLi.addClassName('active');
        });

        if (scroll) {
            var visibleTab = tabs.detect(function(el) {
                return el.getStyle('display') !== 'none';
            });
            if (visibleTab) {
                visibleTab.scrollTo();
            }
        }

        return tab;
    },

    /**
     * @param {String} tab      Tab to deactivate
     * @return {String|false}   Last deactivated tab or false if tab not found
     */
    deactivate: function(tab) {
        if (!tab) {
            while (this.activeTabs.length) {
                this.deactivate(this.activeTabs[0]);
            }
            return tab;
        }

        var index = this.activeTabs.indexOf(tab);
        if (index > -1) {
            this.activeTabs.splice(index, 1);
        }

        var content = $(this.tpl.content.replace(this.tpl.tab, tab));
        if (!content) {
            return false;
        }

        content.removeClassName('active');
        content.hide();

        var href = this.tpl.href.replace(this.tpl.tab, tab),
            tabs = this.container.select(this.config.tabs + '[href="' + href + '"]');

        tabs.each(function(a) {
            a.removeClassName('active');
            var parentLi = a.up('li');
            parentLi && parentLi.removeClassName('active');
        });

        return tab;
    },

    /**
     * @param {Object} el       Element
     * @param {Object} e        Event
     * @param {String} tab      Tab to activate
     * @param {Boolean} scroll  Flag to indicate that page should be scrolled to the tab
     */
    onclick: function(el, e, tab, scroll) {
        var isAccordion = false,
            accordionTrigger = $$('.easytabs-a-accordion').first();
        if (accordionTrigger) {
            // accordion tabs are hidden for desktop
            isAccordion = (accordionTrigger.getStyle('display') !== 'none');
        }

        tab    = tab || this.getTabByHref(el.href);
        scroll = scroll || el.hasClassName('easytabs-scroll');
        if (isAccordion) {
            if (el.hasClassName('active')) {
                this.deactivate(tab);
            } else {
                this.activate(tab, scroll);
            }
        } else {
            this.deactivate();
            this.activate(tab, scroll);
        }
    },

    /**
     * Retrieve tab name from the url
     *
     * @param {String} href
     */
    getTabByHref: function(href) {
        var tab = href.match(this.tpl.href + '$');
        if (!tab) {
            return false;
        }
        return tab[1];
    }
};
