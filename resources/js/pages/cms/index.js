if ($('#analytics-index').length) {
    var analytics_vm = new Vue({
        el: '#analytics-index',
        data: {
            account: meta('account_id') ? meta('account_id') : 0,
            account_name: meta('account_name') ? meta('account_name') : '',
            accounts: {0: 'Loading...'},
            new_account: false,
            property: meta('property_id') ? meta('property_id') : 0,
            property_name: meta('property_name') ? meta('property_name') : '',
            properties: {0: 'Loading...'},
            new_property: false,
        },
        created: function () {
            var me = this;

            if (me.account == 0) {
                me.post_get_accounts();
            }
        },
        watch: {
            account: function () {
                var me = this;

                if (me.account == 1) {
                    me.new_account = true;
                }
                else if (me.account != 0) {
                    me.post_get_web_properties();
                }

                me.account_name = me.accounts[me.account];
            },
            property: function () {
                var me = this;

                if (me.property == 1) {
                    me.new_property = true;
                }
                else if (me.property != 0) {
                    // ready to hit save
                }

                me.property_name = me.properties[me.property];
            },
        },
        methods: {
            reset: function () {
                var me = this;

                me.reset_accounts();
            },
            reset_accounts: function () {
                var me = this;

                me.account = 0;
                me.accounts = {0: 'Loading...'};

                me.reset_properties();
            },
            reset_properties: function () {
                var me = this;

                me.property = 0;
                me.properties = {0: 'Loading...'};
            },


            // ACCOUNTS
            post_get_accounts: function () {
                var me = this;

                me.reset_accounts();

                axios.post('/cms/analytics/accounts', {
                        _token: meta('csrf-token'),
                    })
                    .then(function (response) {
                        if (response.data.success) {
                            me.accounts = Object.assign({0: 'Choose your account'}, /*{1: 'Create new account'},*/ response.data.accounts);
                            me.account = 0;
                        }
                    });
            },
            post_new_account: function (account_name) {
                var me = this;

                axios.post('/cms/analytics/create-account', {
                        _token: meta('csrf-token'),
                        account_name: account_name
                    })
                    .then(function (response) {
                        if (response.data.success) {
                            me.post_get_accounts();
                            me.account = response.data.account_id;
                        }
                    });
            },


            // PROPERTIES
            post_get_web_properties: function () {
                var me = this;

                me.reset_properties();

                axios.post('/cms/analytics/account-properties', {
                        _token: meta('csrf-token'),
                        account: me.account
                    })
                    .then(function (response) {
                        if (response.data.success) {
                            me.properties = Object.assign({0: 'Choose your property'}, /*{1: 'Create new property'},*/ response.data.properties);
                            me.property = 0;
                        }
                    });
            },
            post_new_property: function (property_name) {
                var me = this;

                axios.post('/cms/analytics/create-property', {
                        _token: meta('csrf-token'),
                        account: me.account,
                        property_name: property_name
                    })
                    .then(function (response) {
                        if (response.data.success) {
                            me.post_get_web_properties();
                            me.account = response.data.property_id;
                        }
                    });
            },
        },
    });
}
