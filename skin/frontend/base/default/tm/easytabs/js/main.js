var EasyTabs = Class.create();
EasyTabs.prototype = {
    tpl: {
        tab    : '(.+)?',
        href   : '#product_tabs_(.+)?',
        content: 'product_tabs_(.+)?_contents'
    },
    config: {
        tabs     : '.easytabs-anchor',
        scrollSpeed: 0.5,
        scrollOffset: -5
    },

    initialize: function(container, options) {
        Object.extend(this.config, options || {});
        this.container = container;
        this.activeTabs = [];
        this.counters = {}; // Activity counters

        var isActivateFirstTab = !this.isExpandedLayout()
            && !this.container.hasAttribute('data-collapsed');

        if (this.container.hasAttribute("data-track-hash") && window.location.hash.length > 1) {
            if (this.activate(this.getTabByHref(window.location.hash), true)) {
                // some tab already activated so do not activate first tab
                isActivateFirstTab = false;
            }
        }

        Event.observe(window, "hashchange", function() {
            var href = window.location.hash;
            if (href.length <= 1) {
                var first = this.container.down(this.config.tabs);
                href = first.href || first.readAttribute('data-href');
            } else {
                if (-1 === href.indexOf('#tab_')) {
                    return;
                }
            }
            this.deactivate();
            this.activate(this.getTabByHref(href));
        }.bind(this));

        if (isActivateFirstTab) {
            var first = this.container.down(this.config.tabs);
            if ('undefined' !== typeof first) {
                this.activate(this.getTabByHref(first.href || first.readAttribute('data-href')));
            }
        }

        this.container.select(this.config.tabs).each(function(el ,i) {
            el.observe('click', this.onclick.bind(this, el));
            el.addClassName('easytabs-inited');

            var id = $(el).getAttribute('data-href');
            if (!id) {
                return;
            }
            $$(id + '_contents .pages a').each(function(_el){
                if (-1 == _el.href.indexOf("#")
                    && -1 !== _el.href.indexOf(window.location.host)) {

                    _el.href = _el.href + id;
                }
            });
        }.bind(this));
    },

    /**
     * @param {String} tab      Tab to activate
     * @param {Boolean} scroll  lag to indicate that page should be scrolled to the tab
     * @return {String|false}   Activated tab of false if tab wasn't found
     */
    activate: function(tab, scroll, animate) {
        var tabContentId = this.tpl.content.replace(this.tpl.tab, tab);
        tabContentId = tabContentId.replace(/\./g, '\\.'); // allow id with period

        var content = this.container.down('#' + tabContentId);
        if (!content) {
            return false;
        }

        document.fire('easytabs:beforeActivate', {
            'tab'     : tab,
            'content' : content,
            'easytabs': this
        });

        if (-1 === this.activeTabs.indexOf(tab)) {
            this.activeTabs.push(tab);
        }

        if (this.isExpandedLayout()) {
            this.scrollToTab(tab, content, animate);
        } else {
            this.openTab(tab, content, scroll, animate);
        }

        document.fire('easytabs:afterActivate', {
            'tab'     : tab,
            'content' : content,
            'easytabs': this
        });

        return tab;
    },

    openTab: function (tab, content, scroll, animate) {
        this._updateCounter(tab);
        content.addClassName('active');
        content.show();

        var href = this.tpl.href.replace(this.tpl.tab, tab),
            tabs = this.container.select(
                this.config.tabs + '[href="' + href + '"]',
                this.config.tabs + '[data-href="' + href + '"]'
            );

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
                Effect.ScrollTo(visibleTab, {
                    duration: animate ? this.config.scrollSpeed : 0,
                    offset: this.config.scrollOffset
                });
            }
        }
    },

    scrollToTab: function (tab, content, animate) {
        // debugger;
        Effect.ScrollTo(content, {
            duration: animate ? this.config.scrollSpeed : 0,
            offset: this.config.scrollOffset
        });
    },

    /**
     * @param {String} tab      Tab to deactivate
     * @return {String|false}   Last deactivated tab or false if tab not found
     */
    deactivate: function(tab) {
        debugger;
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

        if (this.isExpandedLayout()) {
            // do nothing if expanded layout enabled
            return tab;
        }

        var tabContentId = this.tpl.content.replace(this.tpl.tab, tab);
        tabContentId = tabContentId.replace(/\./g, '\\.'); // allow id with period

        var content = this.container.down('#' + tabContentId);
        if (!content) {
            return false;
        }

        document.fire('easytabs:beforeDeactivate', {
            'tab'     : tab,
            'content' : content,
            'easytabs': this
        });

        content.removeClassName('active');
        content.hide();

        var href = this.tpl.href.replace(this.tpl.tab, tab),
            tabs = this.container.select(
                this.config.tabs + '[href="' + href + '"]',
                this.config.tabs + '[data-href="' + href + '"]'
            );

        tabs.each(function(a) {
            a.removeClassName('active');
            var parentLi = a.up('li');
            parentLi && parentLi.removeClassName('active');
        });

        document.fire('easytabs:afterDeactivate', {
            'tab'     : tab,
            'content' : content,
            'easytabs': this
        });

        return tab;
    },

    /**
     * @param {Object} el       Element
     * @param {Object} e        Event
     * @param {String} tab      Tab to activate
     * @param {Boolean} scroll  Flag to indicate that page should be scrolled to the tab
     */
    onclick: function(el, e, tab, scroll, animate) {
        var isAccordion = false,
            accordionTrigger = this.container.down('.easytabs-a-accordion');
        if (accordionTrigger) {
            // accordion tabs are hidden for desktop
            isAccordion = (accordionTrigger.getStyle('display') !== 'none');
        }

        tab    = tab || this.getTabByHref(el.href || el.readAttribute('data-href'));
        scroll = scroll || el.hasClassName('easytabs-scroll');
        animate = animate || el.hasClassName('easytabs-animate');

        if (isAccordion) {
            if (el.hasClassName('active')) {
                this.deactivate(tab);
            } else {
                this.activate(tab, scroll, animate);
            }
        } else {
            this.deactivate();
            this.activate(tab, scroll, animate);
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
    },

    /**
     * Update activation counter
     *
     * @param  {String} tab
     * @return void
     */
    _updateCounter: function(tab) {
        if (!this.counters[tab]) {
            this.counters[tab] = 0;
        }
        this.counters[tab]++;
    },

    /**
     * Retreive activation count for specified tab
     *
     * @param  {String} tab
     * @return {Integer}
     */
    getActivationCount: function(tab) {
        if (!this.counters[tab]) {
            this.counters[tab] = 0;
        }
        return this.counters[tab];
    },

    isExpandedLayout: function () {
        return $(this.container).hasClassName('expanded');
    }
};

document.observe('dom:loaded', function(){
    window.easytabs = [];
    $$('.easytabs-wrapper').each(function (container){
        window.easytabs.push(new EasyTabs(container));
    })
    // initialize custom links
    if (easytabs.length) {
        var linkSelector = easytabs.first().config.tabs;
        $$(linkSelector).each(function (customLink){
            if (customLink.hasClassName('easytabs-inited')) {
                return;
            };
            customLink.observe('click', function(event) {
                var element = this;
                easytabs.each(function (tabs){
                    tab = tabs.getTabByHref(element.readAttribute('href'));
                    if (tab) {
                        tabs.onclick(element);
                        event.stop();
                        throw $break;
                    }
                });
            });
            customLink.addClassName('easytabs-inited');
        });
    };
});
