const apiCall = ({
  url,
  data,
  method,
  ...args
}) => new Promise((resolve, reject) => {
  setTimeout(() => {
    try {
      if (method === 'POST') {
        const curentUserName = data.username;
        resolve({
          token: '$2y$10$.vGA1O9wmRjrwAVXD98HNOgsNpDczlqm3Jq7KnEd1rVAGv3Fykk1a',
          login: curentUserName
        })

      } else {
        const login = localStorage.getItem('user-login') || 'unkown';
        resolve({
          name: login
        })
      }
    } catch (err) {
      reject(new Error(err))
    }
  }, 1000)
})

export default apiCall
