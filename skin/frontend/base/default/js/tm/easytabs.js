var EasyTabs = Class.create();
EasyTabs.prototype = {
    tpl: {
        tab    : '(.+)?',
        href   : '#product_tabs_(.+)?',
        content: 'product_tabs_(.+)?_contents'
    },
    config: {
        tabs     : '.easytabs-anchor',
        container: '#easytabs',
        trackHashValue: true
    },
    container : false,

    /**
     * Tabs that are active at this moment
     * Multiple tabs allowed in accordion mode
     *
     * @type {Array}
     */
    activeTabs: [],

    /**
     * Activity counters
     * {
     *   tab_id: activation_count,
     *   ...
     * }
     *
     * @type {Object}
     */
    counters: {},

    initialize: function(options) {
        Object.extend(this.config, options || {});
        this.container = $$(this.config.container).first();

        if (this.config.trackHashValue && window.location.hash.length > 1) {
            this.activate(this.getTabByHref(window.location.hash), true);
            Event.observe(window, "load", function() {
                this.activate(this.getTabByHref(window.location.hash), true);
            }.bind(this));
        }

        Event.observe(window, "hashchange", function() {
            var href = window.location.hash;
            if (href.length <= 1) {
                var first = this.container.down(this.config.tabs);
                href = first.href || first.readAttribute('data-href');
            } else {
                if (-1 === href.indexOf('#product_tabs_')) {
                    return;
                }
            }
            this.deactivate();
            this.activate(this.getTabByHref(href));
        }.bind(this));

        if (!this.activeTabs.length) {
            var first = this.container.down(this.config.tabs);
            this.activate(this.getTabByHref(first.href || first.readAttribute('data-href')));
        }

        $$(this.config.tabs).each(function(el ,i) {
            el.observe('click', this.onclick.bind(this, el));
            
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
    activate: function(tab, scroll) {
        var content = $(this.tpl.content.replace(this.tpl.tab, tab));
        if (!content) {
            return false;
        }

        document.fire('easytabs:beforeActivate', {
            'tab'     : tab,
            'content' : content,
            'easytabs': this
        });

        this._updateCounter(tab);
        content.addClassName('active');
        content.show();

        if (-1 === this.activeTabs.indexOf(tab)) {
            this.activeTabs.push(tab);
        }

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
                visibleTab.scrollTo();
            }
        }

        document.fire('easytabs:afterActivate', {
            'tab'     : tab,
            'content' : content,
            'easytabs': this
        });

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
    onclick: function(el, e, tab, scroll) {
        var isAccordion = false,
            accordionTrigger = $$('.easytabs-a-accordion').first();
        if (accordionTrigger) {
            // accordion tabs are hidden for desktop
            isAccordion = (accordionTrigger.getStyle('display') !== 'none');
        }

        tab    = tab || this.getTabByHref(el.href || el.readAttribute('data-href'));
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
    }
};
