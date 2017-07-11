if ($('#analytics-index').length) {
    var playlist_vm = new Vue({
        el: '#analytics-index',
        data: {
            account: null,
            accounts: [],
        },
        created: function(){

        },
        methods: {
            accounts: function(){
                var me = this;

                axios.post('/api/cms/analytics/accounts', {
                        _token: meta('csrf-token'),
                    })
                    .then(function (response) {
                        if (response.data.success) {
                            me.accounts = response.data.accounts;
                        }
                    });
            }
        },
    });
}
