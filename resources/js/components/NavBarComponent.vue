
<style lang="scss" scoped>
    @import '../../sass/variables';
    .navbar{
        height: 5rem;
        background-color: $primary-color;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .navbar-brand{
        font-size: 3em;
        font-family: 'Roboto Slab',sans-serif;
        display: flex;
        align-items: center;

        .brand-S{
            border: 1px black solid;
            border-radius: 15%;
            width: 3rem;
            height: 4rem;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 0.2rem;
        }
        .brand-CRAMBLE{
            font-size: 0.8em;
            letter-spacing: 0.2rem;
        }
    }


</style>

<template>
    <nav class="navbar">
            <div class="navbar-brand" v-on:click="alert()">
                <div class="brand-S">
                    S
                </div>
                <div class="brand-CRAMBLE">
                    CRAMBLE
                </div>
            </div>
    </nav>
</template>

<script>
export default {
    methods: {
        alert() {
            alert('test')
        }
    }
}
</script>
