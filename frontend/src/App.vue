<template>
    <div class="app">
        <h1 class="title">Formularz użytkownika</h1>
        <UserForm />
    </div>
</template>

<script setup>
import UserForm from "./components/UserForm.vue";
</script>

<style>
body {
    font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    margin: 0;
    padding: 0;
    min-height: 100vh;
}

.app {
    max-width: 800px;
    margin: 50px auto;
    padding: 0 20px;
}

.title {
    text-align: center;
    color: #ffffff;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 40px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
    letter-spacing: -0.5px;
}
</style>
