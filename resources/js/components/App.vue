<template>
    <!-- Плашка-уведомление (Bootstrap alert) -->
    <div
        v-if="article"
        class="alert alert-primary alert-dismissible fade show notification-alert"
        role="alert"
    >
        Добавлена новая статья:
        <strong>
            <a :href="articleUrl" class="alert-link">{{ article.title }}</a>
        </strong>

        <button type="button" class="btn-close" aria-label="Close" @click="close"></button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            article: null,
        };
    },

    computed: {
        articleUrl() {
            return this.article ? `/articles/${this.article.id}` : '#';
        },
    },

    created() {
        // Публичный канал "test" и событие NewArticleEvent
        // Данные приходят из broadcastWith(): { article: { id, title, ... } }
        window.Echo.channel('test')
            .listen('NewArticleEvent', (payload) => {
                this.article = payload.article;

                // автозакрытие через 8 секунд
                window.clearTimeout(this._t);
                this._t = window.setTimeout(() => this.close(), 8000);
            });
    },

    beforeUnmount() {
        window.clearTimeout(this._t);
    },

    methods: {
        close() {
            this.article = null;
        },
    },
};
</script>

<style scoped>
.notification-alert {
    position: fixed;
    top: 12px;
    right: 12px;
    z-index: 9999;
    max-width: 420px;
}
</style>
