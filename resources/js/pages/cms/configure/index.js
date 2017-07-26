if ($('#configure').length) {
    var analytics_vm = new Vue({
        el: '#configure',
        data: {
            config: null,
            disable_watchers: false,
            completed_steps: {
                1: false, // project name
                2: false, // google login credentials
                3: meta('logged_id') == 'true', // logged in
                4: false, // enabled api's
                5: false, // service_account_credentials_json
                6: false, // account, property, view
                7: false, // user permission added
            },

            account_id: 0,
            account_name: null,
            accounts: {0: 'Loading...'},
            new_account: false,
            new_account_name: null,

            property_id: 0,
            property_name: null,
            properties: {0: 'Loading...'},
            new_property: false,
            new_property_name: null,
        },
        created: function () {
            var me = this;

            me.post_get_config(function(){
                me.disable_watchers = true;
                me.account_id = me.config.account_id;
                me.account_name = me.config.account_name;
                me.property_id = me.config.property_id;
                me.property_name = me.config.property_name;
                Vue.nextTick(function(){
                    me.disable_watchers = false;
                });
            });
        },
        watch: {
            config: function () {
                var me = this;

                me.update_steps();
            },
            account_id: function () {
                var me = this;

                if( ! me.disable_watchers ){
                    me.account_name = me.accounts[me.account_id];

                    if (me.account_id == 1) {
                        me.new_account = true;
                    }
                    else if (me.account_id != 0) {
                        me.post_save_account();
                        me.post_get_web_properties();
                    }
                }

            },
            property_id: function () {
                var me = this;

                if ( ! me.disable_watchers ) {
                    me.property_name = me.properties[me.property_id];

                    if (me.property_id == 1) {
                        me.new_property = true;
                    }
                    else if (me.property_id != 0) {
                        me.post_save_property();
                    }
                }
            },
        },
        methods: {
            post_get_config: function(callback){
                var me = this;

                axios.post('/cms/analytics/config', {
                        _token: meta('csrf-token'),
                    })
                    .then(function (response) {
                        if (response.data) {
                            me.config = response.data.config;
                            callback();
                        }
                    });
            },
            update_steps: function () {
                var me = this;

                if( me.config.project_name ){
                    me.completed_steps[1] = true;
                }
                if (me.config.client_id && me.config.client_secret) {
                    me.completed_steps[2] = true;
                }
                if (me.config.apis_enabled) {
                    me.completed_steps[4] = true;
                }
                if (me.config.service_account_credentials_json) {
                    me.completed_steps[5] = true;
                }
                if ( me.config.account_id && me.config.property_id && me.config.view_id ) {
                    me.completed_steps[6] = true;
                }
                if ( me.config.analytics_user_added ) {
                    me.completed_steps[7] = true;
                }
            },
            completed_steps_up_to: function (step) {
                var me = this;

                for(var i=1; i<step; i++){
                    if( !me.completed_steps[i] ){
                        return false;
                    }
                }

                return true;
            },

            // Save Project Name
            set_project_name: function (project_name) {
                var me = this;

                axios.post('/cms/analytics/configure/project-name', {
                        _token: meta('csrf-token'),
                        project_name: project_name,
                    })
                    .then(function (response) {
                        if (response.data.success) {
                            me.config = response.data.config;
                        }
                    });
            },

            // Save Client Credentials
            set_login_credentials: function (client_id, client_secret) {
                var me = this;

                axios.post('/cms/analytics/configure/login-credentials', {
                        _token: meta('csrf-token'),
                        client_id: client_id,
                        client_secret: client_secret,
                    })
                    .then(function (response) {
                        if (response.data.success) {
                            me.config = response.data.config;
                        }
                    });
            },


            // ACCOUNTS & PROPERTIES
            post_get_accounts: function () {
                var me = this;

                me.reset_accounts();

                axios.post('/cms/analytics/configure/accounts', {
                        _token: meta('csrf-token'),
                    })
                    .then(function (response) {
                        if (response.data.success) {
                            me.accounts = Object.assign({0: 'Choose your account'}, {1: 'Create new account'}, response.data.accounts);
                            me.account_id = 0;
                        }
                    });
            },
            post_new_account: function (account_name) {
                var me = this;

                axios.post('/cms/analytics/configure/create-account', {
                        _token: meta('csrf-token'),
                        account_name: account_name
                    })
                    .then(function (response) {
                        if (response.data.success) {
                            me.post_get_accounts();
                            me.account_id = response.data.account_id;
                        }
                    });
            },
            // Save Account
            post_save_account: function () {
                var me = this;

                axios.post('/cms/analytics/configure/save-account', {
                        _token: meta('csrf-token'),
                        account_id: me.account_id,
                        account_name: me.account_name,
                    })
                    .then(function (response) {
                        if (response.data.success) {
                            me.config = response.data.config;
                        }
                    });
            },

            post_get_web_properties: function () {
                var me = this;

                me.reset_properties();

                axios.post('/cms/analytics/configure/account-properties', {
                        _token: meta('csrf-token'),
                        account_id: me.account_id
                    })
                    .then(function (response) {
                        if (response.data.success) {
                            me.properties = Object.assign({0: 'Choose your property'}, {1: 'Create new property'}, response.data.properties);
                            me.property_id = 0;
                            window.a = me.properties;
                        }
                    });
            },
            post_new_property: function (property_name) {
                var me = this;

                axios.post('/cms/analytics/configure/create-property', {
                        _token: meta('csrf-token'),
                        account_id: me.account_id,
                        property_name: property_name
                    })
                    .then(function (response) {
                        if (response.data.success) {
                            me.post_get_web_properties();
                            me.account = response.data.property_id;
                        }
                    });
            },
            // Save Property
            post_save_property: function () {
                var me = this;

                axios.post('/cms/analytics/configure/save-property', {
                        _token: meta('csrf-token'),
                        property_id: me.property_id,
                        property_name: me.property_name,
                    })
                    .then(function (response) {
                        if (response.data.success) {
                            me.config = response.data.config;
                        }
                    });
            },

            reset: function () {
                var me = this;

                me.reset_accounts();
            },
            reset_accounts: function () {
                var me = this;

                me.account_id = 0;
                me.accounts = {0: 'Loading...'};

                me.reset_properties();
            },
            reset_properties: function () {
                var me = this;

                me.property_id = 0;
                me.properties = {0: 'Loading...'};
            },

        },
    });
}
