<template>
    <div>
        <div class="container">
            <div class="card rounded shadow-sm mb-4">
                <div class="card-body">
                    <form @submit.prevent="addArticle" class="mb-2">
                        <div class="form-group">
                            <label for="title">Article Title</label>
                            <input type="text" class="form-control" v-model="article.title" name="title">
                        </div>
                        <div class="form-group">
                            <label for="body">Article Content</label>
                            <textarea class="form-control" id="body" rows="3" v-model="article.body" ></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-light btn-block">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item mb-3 mt-2 shadow-sm rounded">
                <a class="page-link" href="#" @click="fetchArticles(pagination.prev_page_url)">Previous</a>
                </li>

                <li class="page-item disabled mt-2"><a class="page-link text-dark" href="#">Page {{ pagination.current_page }} of {{ pagination.last_page }}</a></li>

                <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item mb-3 mt-2 shadow-sm rounded">
                <a class="page-link" href="#" @click="fetchArticles(pagination.next_page_url)">Next</a>
                </li>
            </ul>
        </nav>

        <div class="card border-light mb-3 rounded shadow mb-5 bg-white " v-for="article in articles" v-bind:key="article.id">
            <div class="card-header">{{ article.title }}</div>
            <div class="card-body">
                <!-- <h4 class="card-title">{{ article.title }}</h4> -->
                <p class="card-text">{{ article.body }}</p>
                <hr>
                <button @click="deleteArticle(article.id)" class="btn btn-danger" type="button">Delete</button>
                <button @click="editArticle(article)" class="btn btn-outline-warning" type="button">Edit</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return {
            articles: [], //return an empty array to start with
            article: { //an article which is a singel object
                id: '',
                title: '',
                body: ''
            },
            article_id: '', //when we send a put request(update) we have to send this field so it knows which article to update
            pagination: {}, //an object
            edit: false, //our edit state will be false by default
        }
    },

    created() {
        this.fetchArticles(); //when the page loads, it will run this method automatically
    },

    methods: {
        fetchArticles(page_url){
            let vm = this;
            page_url = page_url || '/api/articles'
            fetch(page_url) //it will give us a response but not the data
                .then(res => res.json()) // we have to map that to json
                .then(res => { //and this gives us an object with the data property that has the aricles array as its value
                    // console.log(res.data);
                    this.articles = res.data; //assign it to the articles array above, res.link will give us pagination
                    vm.makePagination(res.meta, res.links);
                })
                .catch(err => console.log(err));
        },
        makePagination(meta, links){
            let pagination = { //create a pagination object and set it to the pagination above
                current_page: meta.current_page,
                last_page: meta.last_page,
                next_page_url: links.next,
                prev_page_url: links.prev,
            };

            this.pagination = pagination;
        },
        deleteArticle(id) {
            if(confirm('Are Your Sure?')) {
                fetch(`api/article/${id}`, {
                    method: 'delete'
                })
                .then(res => res.json())
                .then(data => {
                    alert('Article Removed');
                    this.fetchArticles();
                    })
                .catch(err => console.log(err));
            }
        },
        addArticle() {
            if(this.edit === false)  {
                //add
                fetch('api/article', {
                    method: 'post',
                    body: JSON.stringify(this.article),
                    headers: {
                        'content-type':'application/json'
                    }
                })
                .then(res => res.json()) //take our response
                .then(data => {
                    this.article.title = ''; // equals nothing
                    this.article.body = '';
                    alert('Article Added');
                    this.fetchArticles(); //call it again
                }) //take the data and clear the form
                .catch(err => consoles.log(err));
            } else {
                //update
                fetch('api/article', {
                    method: 'put',
                    body: JSON.stringify(this.article),
                    headers: {
                        'content-type':'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    this.article.title = '';
                    this.article.body = '';
                    alert('Article Updated');
                    this.fetchArticles();
                })
                .catch(err => console.log(err));
            }
        },
        editArticle(article) { //takes in entire article object and set it to the properties above
            this.edit = true;
            this.article.id = article.id;
            this.article.article_id = article.id;
            this.article.title = article.title;
            this.article.body = article.body;
            //then it gonna run addarticle
        }
    }
}
</script>

