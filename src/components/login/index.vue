<template>
  <div class="d-flex h-100 justify-content-center align-items-center">
    <b-form @submit.prevent="login" class="login-form" validated: true>
      <h2 class="text-center">Войти в систему</h2>
      <h6 class="text-center">Пожалуйста введите свой логин и пароль</h6>
      <b-form-group id="loginInputGroup" label="Логин:" label-for="loginInput">
        <b-form-input id="loginInput" type="text" v-model="username" required placeholder="Введите имя пользователя">
        </b-form-input>
      </b-form-group>
      <b-form-group id="passwordInputGroup" label="Пароль:" label-for="passwordInput">
        <b-form-input id="passwordInput" type="password" v-model="password" required placeholder="Введите пароль">
        </b-form-input>
      </b-form-group>
      <b-form-row>
        <b-col>
          <b-button type="submit" variant="success" class="btn-block">Войти</b-button>
        </b-col>
      </b-form-row>
      <b-form-row class="mt-2">
        <b-col class="text-center">
          <b-link to="registration">Зарегистрироваться</b-link>
        </b-col>
      </b-form-row>
    </b-form>
  </div>
</template>

<style lang="scss">
.login-form {
  width: 25rem;
  padding-bottom: 10rem;
}
</style>

<script>
import { AUTH_REQUEST } from "../../store/actions/auth";


export default {
  name: "login",
  data() {
    return {
      username: "",
      password: ""
    };
  },
  methods: {
    login: function() {
      const { username, password } = this;
      
      this.$store
        .dispatch(AUTH_REQUEST, {
          username,
          password
        })
        .then(() => {
          this.$router.push("/");
        });
    }
  }
};
</script>
