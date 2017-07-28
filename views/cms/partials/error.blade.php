<div class="modal fade" id="analytics-error" tabindex="-1" role="dialog" aria-labelledby="analytics-error">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" v-on:click="close()">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">An Error has occurred</h3>
                <h4>@{{ title }}</h4>
            </div>
            <div class="modal-body">
                @{{ text }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" v-on:click="close()">Close</button>
            </div>
        </div>
    </div>
</div>
