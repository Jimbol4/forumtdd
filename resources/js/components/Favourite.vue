<template>
    <button type="submit" :class="classes" @click="toggle">
        <span class="glyphicon glyphicon-heart"></span>
        <span v-text="count"></span>
    </button>
</template>

<script>
export default {
    props: ['reply'],

    data() {
        return {
            count: this.reply.favoritesCount,
            isFavourited: this.reply.isFavourited
        }
    },

    computed: {
        classes() {
            return ['btn', this.isFavourited ? 'btn-primary' : 'btn-default'];
        },

        endpoint() {
            return '/replies/' + this.reply.id + '/favourites';
        }
    },

    methods: {
        toggle() {
           return this.isFavourited ? this.destroy() : this.create();
        },

        destroy() {
            axios.delete(this.endpoint);
            this.isFavourited = false;
            this.count--;
        },

        create() {
            axios.post(this.endpoint);
            this.isFavourited = true;
            this.count++;
        }
    }
}
</script>
