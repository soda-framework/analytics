<div :class="['step', config && config.project_name && completed_steps[{{ $step }}] ? 'completed' : '']">
    <h3 class="">Project Name</h3>
    <div v-if="config" class="step-content">
        <ul>
            <li>
                <a href="https://console.developers.google.com/projectcreate" target="_blank">Create</a>
                or
                <a href="https://console.developers.google.com/apis/library?project={{ $config->project_name }}"
                   target="_blank">Choose</a>
                a project on Google Console
            </li>
            <li>
                Enable the
                <a :href="'https://console.developers.google.com/apis/api/servicemanagement.googleapis.com/overview?project='+config.project_name" target="_blank">Service Management API</a>
            </li>
            <li>
                Enter your project name below and save.
            </li>
        </ul>

        <input name="project_name" type="text" v-model="config.project_name" class="form-control">

        <div v-on:click="set_project_name(config.project_name)" class="btn btn-primary">SAVE</div>
    </div>

    <div v-on:click="completed_steps[{{ $step }}] = !completed_steps[{{ $step }}];" class="edit-step fa fa-cog"></div>
</div>
