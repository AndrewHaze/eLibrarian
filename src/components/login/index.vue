<template>
  <div class="d-flex h-100 justify-content-center align-items-center">
    <b-form @submit.prevent="login" class="login-form" validated: true>
      <h2 class="text-center">Войти в систему</h2>
      <h6 class="text-center">Пожалуйста введите свой логин и пароль</h6>
      <b-form-group id="loginInputGroup" label="Логин:" label-for="loginInput">
        <b-form-input id="loginInput" type="text" :state="loginState" v-model="username" placeholder="Введите имя пользователя">
        </b-form-input>
        <b-form-invalid-feedback id="inputLiveFeedback1">
          <!-- This will only be shown if the preceeding input has an invalid state -->
          {{ usernameErrorMessage }}
        </b-form-invalid-feedback>
      </b-form-group>
      <b-form-group id="passwordInputGroup" label="Пароль:" label-for="passwordInput">
        <b-form-input id="passwordInput" type="password" :state="passwordState" v-model="password" placeholder="Введите пароль">
        </b-form-input>
        <b-form-invalid-feedback id="inputLiveFeedback2">
          <!-- This will only be shown if the preceeding input has an invalid state -->
          {{ passwordErrorMessage }}
        </b-form-invalid-feedback>
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
  import store from "../../store";
  import {
    AUTH_REQUEST
  } from "../../store/actions/auth";
  
  export default {
    name: "login",
    data() {
      return {
        username: "",
        usernameErrorMessage: "Пожалуйста заполните это поле",
        loginState: null,
        password: "",
        passwordErrorMessage: "Пожалуйста заполните это поле",
        passwordState: null,
      };
    },
    created: function() {
      const self = this;
      //Вызов функции из глобального миксина
      this.callApi(
        this.$store.getters.prefix + "/static/api.php", {
          cmd: "first", //проверяем наличие зарег. пользователей
          dat: ""
        },
        function(rd) {
          if (rd) {
            //нет зарег. пользователей
            store.commit("checkUsers", false);
            self.$router.push("/registration");
          } else {
            store.commit("checkUsers", true);
          }
        }
      );
    },
    methods: {
      verification: function() {
        const {
          username,
          password
        } = this;
        const self = this;
        this.callApi(
          this.$store.getters.prefix + "/static/api.php", {
            cmd: "verification", //проверяем наличие зарег. пользователей
            usr: username,
            psw: password
          },
          function(rd) {
            if (rd) {
              store.commit("showRegModal", false);
              self.$store
                .dispatch(AUTH_REQUEST, {
                  username,
                  password
                })
                .then(() => {
                  self.$router.push("/");
                });
            } else {
              self.loginState = true;
              self.passwordState = false;
              self.passwordErrorMessage =
                "Неправильный пароль!";
            }
          }
        );
  
      },
      login: function() {
        const self = this;
        const {
          username,
          password
        } = this;
        let flag;
        let problems = false;
        if (username.length < 1) {
          problems = true;
          this.loginState = false;
        } else {
          problems = false;
          this.loginState = true;
        }
        if (password.length < 1) {
          problems = true;
          this.passwordState = false;
        } else {
          problems = false;
          this.passwordState = true;
        }
        if (problems) return;
  
        //Вызов функции из глобального миксина
        this.callApi(
          this.$store.getters.prefix + "/static/api.php", {
            cmd: "exist", //проверяем наличие зарег. пользователей
            dat: username
          },
          function(rd) {
            if (rd) {
              self.loginState = false;
              self.passwordState = true;
              self.usernameErrorMessage =
                "Пользователь с таким именем не существует!";
            } else {
              self.verification();
  
            }
          }
        );
      }
    }
  };
</script>
