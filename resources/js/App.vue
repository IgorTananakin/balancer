<template>
  <div id="app">
    <!-- Заменяем Dashboard на router-view -->
    <router-view :user-email="userEmail" :is-history-page="isHistoryPage" />
  </div>
</template>

<script>
export default {
  name: 'App',
  data() {
    return {
      userEmail: document.getElementById('app')?.dataset?.userEmail || 'ivan@example.com',
      isHistoryPage: document.getElementById('app')?.dataset?.isHistoryPage === 'true'
    }
  }
}
</script>

<style>
/* Базовые стили приложения */
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}
</style>