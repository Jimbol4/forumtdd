<template>
    <div :id="'reply-' +id" class="card">
        <div class="card-header">
            <div class="level">
                <h5 class="flex">
                    <a :href="'/profiles/'+data.owner.name" v-text="data.owner.name"></a> said <span v-text="ago"></span>
                </h5>

                <div v-if="signedIn">
                    <favourite :reply="data"></favourite>
                </div>
  
            </div>
        </div>
        <div class="card-body">
            <div v-if="editing">
                <textarea class="form-control" v-model="body"></textarea>

                <button class="btn btn-xs btn-primary" @click="update">Update</button>
                <button class="btn btn-xs btn-link" @click="editing = false">Cancel</button>
            </div>
            <div v-else v-text="body"></div>
        </div>
        <div v-if="canUpdate">
            <button class="btn btn-xs mr-1" @click="editing = true">Edit</button>
            <button class="btn btn-xs mr-1" @click="destroy">Delete</button> 
        </div>
    </div>
</template>

<script>
    import Favourite from './Favourite.vue';
    import moment from 'moment';

    export default {
        props: ['data'],

        components: { Favourite },

        data() {
            return {
                editing: false,
                id: this.data.id,
                body: this.data.body
            };
        },

        computed: {
            signedIn() {
                return window.App.signedIn;
            },

            canUpdate() {
                return this.authorize(user => this.data.user_id == user.id)
            },

            ago() {
                return moment(this.data.created_at).fromNow() + '...';
            }
        },

        methods: {
            update() {
                axios.patch('/replies/' + this.data.id, {
                    body: this.body
                });
                this.editing = false;
                flash('Updated!');
            },

            destroy() {
                axios.delete('/replies/' + this.data.id);
                
                this.$emit('deleted', this.data.id);
            }
        }
    }
</script>