<style lang="scss" scoped>
    @import '../../sass/variables';
    .container{
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }
    .title-sm{
        font-family: sans-serif;
        font-size: 1.5em;
    }
    .word-container{
        display: flex;
        margin: 2rem;

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
    }
    .score-container{
        margin: 2rem;
        font-size: 2em;
        font-family: 'Roboto-Slab',sans-serif;
    }
    .button{
        border: none;
        color: white;
        padding: 1rem 3rem;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 1em;
        cursor: pointer;
        border-radius: 0.3rem;
    }
    .button-container{
        display: grid;
        grid-template-columns: auto auto;
        grid-gap: 1rem;

        .button-submit{
            background-color: $button-color-1;
            grid-column-start: 1;
            grid-column-end: 3;

            &:hover{
                background-color: darken($button-color-1,20%);
            }
        }
        .button-skip{
            background-color: $button-color-3;
            &:hover{
                background-color: darken($button-color-3,20%);
            }
        }
        .button-clear{
            background-color: $button-color-2;
            &:hover{
                background-color: darken($button-color-2,20%);
            }
        }
        .skipped{
            opacity: 50%;
        }
    }
    .hidden-container{
        display: flex;
        flex-direction: column;
        margin: 2rem
    }
    .message{
        font-size: 1.5em;
        font-family: 'Roboto Slab',sans-serif;
        margin-bottom: 1rem;
    }
    .button-next{
        background-color: $button-color-1;
        &:hover{
            background-color: darken($button-color-1,20%);
        }
    }

</style>

<template>
    <div class="container">
        <p class="title-sm">Score : </p>
        <div class="score-container">
            {{score}}
        </div>

        <p class="title-sm">Scrambled : </p>
        <div class="word-container">
            <div class="character char-scrambled"
                v-for="char in scrambled_word" :key="char.id"
                v-bind:class="{ clicked: char.status === 'clicked' }"
                v-on:click="charClick(char.id,char.content)">
            {{char.content}}
            </div>
        </div>

        <p class="title-sm">Answer : </p>
        <div class="word-container">
            <div class="character char-answer"
                v-for="(char,index) in answer_word" :key="index+'a'"
                v-on:click="charUnclick(char.scrambled_id,char.content)">
            {{char.content}}
            </div>
        </div>

        <div class="button-container">
            <button class="button button-submit"
                v-on:click="btnSubmitClick()"
                :disabled="is_skipped || is_correct"
                v-bind:class="{ skipped: is_skipped || is_correct}"
            >
            Submit
            </button>

            <button class="button button-skip"
                v-on:click="btnSkipClick()"
                :disabled="is_skipped || is_correct"
                v-bind:class="{ skipped: is_skipped || is_correct}">
            Skip
            </button>

            <button class="button button-clear"
                v-on:click="btnClearClick()"
                :disabled="is_skipped || is_correct"
                v-bind:class="{ skipped: is_skipped || is_correct}">
            Clear
            </button>
        </div>

        <div class="hidden-container">
            <div class="message">
                {{message}}
            </div>
            <button class="button button-next"
                v-if="is_skipped || is_correct"
                v-on:click="btnNextClick()">
            Next
            </button>
        </div>
    </div>
</template>

<script>
import shuffle from 'lodash/shuffle';

export default {
    data() {
        clicked: true
        return {
            score: 0,
            word: '',
            scrambled_word: '',
            answer_word: '',
            is_skipped: false,
            is_correct: false,
            message: ''
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
            .get('api/Word/Medium   ')
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
        },
        btnSubmitClick() {
            let answer = ''
            this.answer_word.forEach(element => {
                answer += element.content
            })
            let word_length = this.word.length
            if(answer === this.word) {
                this.is_correct = true
                this.message = 'Your answer is correct'
                this.score += (word_length + (word_length * word_length / 10)) * 100
            }
            else {
                this.message = 'Your answer is wrong'
                this.score -= (word_length * word_length / 10) * 100
            }
        },
        btnClearClick() {
            this.initAnswer()
            this.scrambleWord()
        },
        btnSkipClick() {
            //mesage
            this.message = 'You skipped this word'
            //disable button
            this.is_skipped = true;
            //fill the answer
            let temparray = []
            let answer = this.word.split("")
            let iterator = 0
            answer.forEach(element => {
                temparray.push({content:element, scrambled_id:'', id:iterator})
                iterator++
            })
            this.answer_word = temparray
        },
        btnNextClick() {
            this.fetchWord()
            this.is_skipped = false
            this.is_correct = false
            this.message = ''
        }
    }
}
</script>
