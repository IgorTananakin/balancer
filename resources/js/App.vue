<template>
  <div id="app">
    <router-view :user-email="userEmail" :is-history-page="isHistoryPage" />
  </div>
</template>

<script>
export default {
  name: 'App',
  data() {
    return {
      userEmail: document.getElementById('app')?.dataset?.userEmail || 'ivan@example.com',
      isHistoryPage: document.getElementById('app')?.dataset?.isHistoryPage === 'true',
      isLoginPage: document.getElementById('app')?.dataset?.isLoginPage === 'true'
    }
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}
</style>