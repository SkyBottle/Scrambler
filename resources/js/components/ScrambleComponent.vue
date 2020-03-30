<style scoped>
    .word-container{
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .character{
        display: flex;
        justify-content: center;
        align-items: center;
        border: black 1px solid;
        padding: 0.2rem 0.6rem;
        margin: 0 0.2rem;
        border-radius: 15%;
        width: 3rem;
        height: 4rem;
        font-family: 'Roboto Slab',sans-serif;
        font-size: 3em;
    }
    .clicked{
        opacity: 35%;
    }
</style>

<template>
    <div class="word-container">
        <div class="character char-scrambled"
            v-for="char in scrambled_word" :key="char.id"
            v-bind:class="{ clicked: char.status === 'clicked' }"
            v-on:click="charClick(char.id,char.content)">
            {{char.content}}
        </div>
        <div class="character char-answer"
            v-for="(char,index) in answer_word" :key="index+'a'"
            v-on:click="charUnclick(char.scrambled_id,char.content)">
            {{char.content}}
        </div>
    </div>
</template>

<script>
import shuffle from 'lodash/shuffle';

export default {
    data() {
        clicked: true
        return {
            word: '',
            scrambled_word: '',
            answer_word: '',
            answer_id: ''
        }
    },

    created() {
        this.fetchWord();
    },

    methods: {
        initAnswer() {
            let temparray = []
            let iterator = 0
            this.scrambled_word.forEach(element => {
                temparray.push({content:'', scrambled_id:'', id:iterator})
                iterator++
            })
            this.answer_word = temparray
        },
        scrambleWord() {
            let scrambled = shuffle(this.word.split(""))
            let temparray = []
            let iterator = 0
            scrambled.forEach(element => {
                temparray.push({content: element, status: 'not clicked',id: iterator})
                iterator++
            });
            this.scrambled_word = temparray
            this.initAnswer()
        },
        fetchWord() {
            axios
            .get('api/MediumWord')
            .then(response => {(
                //remove newline from word
                this.word = response.data.toUpperCase().split("\n")[0]);
                this.scrambleWord()
                })
        },
        charClick(id,content) {
            //clicked target
            let target = this.scrambled_word.find((char) => char.id === id)
            //search empty element on answer array
            let answer_target = this.answer_word.find((char) => char.content === '')

            if(target.status === 'not clicked') {
                this.scrambled_word.splice(id,1,{content: content, status: 'clicked', id:id})
                this.answer_word.splice(answer_target.id,1,{content: content, scrambled_id:id, id:answer_target.id})
            }
            else if(target.status === 'clicked') {
                this.charUnclick(id,content)
            }
        },
        charUnclick(id,content) {
            if(id !== '' && content !== '') {
                let answer_target = this.answer_word.find((char) => char.scrambled_id === id)

                this.scrambled_word.splice(id,1,{content: content, status: 'not clicked', id:id})
                this.answer_word.splice(answer_target.id,1,{content: '', scrambled_id: '', id:answer_target.id})
            }
        }
    }
}
</script>
