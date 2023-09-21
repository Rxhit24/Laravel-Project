<template>
    
    <div v-if="notification.show" class="fixed w-[300px] left-4 bottom-4 py-2 px-4 text-white animate-fade-in-down"
    :class="notification.status === 'success' ? 'bg-emerald-500' : 'bg-red-500'">
        {{ notification.message }}
    </div>
</template>

<script>
export default {
    name: 'Notifications',

    data() {
        return {
             

        };
    },
    computed: {
        notification(){
            return this.$store.state.notification;
        }
    }
};
</script>

<style lang="scss" scoped>

</style>