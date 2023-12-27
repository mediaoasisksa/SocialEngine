<div class="container">
      <div class="login">
        <section class="sec-one">
          <!-- <div class="container "> -->
          <h2 class="text-center py-3 head-h-2">Login and explore Coworker</h2>
          <div class="row to-all-login">
            <div class="col-md-1 offset"></div>
            <div class="col-md-5 m-auto">
              <div class="input-group mb-3 text-center">
                <a
                  style="text-decoration: none"
                  class="cont-cont-2 form-control d-flex align-item-center justify-content-center"
                  href="#"
                >
                  <i class="ico-img-2 ico-goo bx bxl-google"></i>
                  <img
                    class="ico-img"
                    style="width: 25px"
                    src="https://img.icons8.com/fluency/48/000000/google-logo.png"
                  />
                  Sign in with Google</a
                >
              </div>
            </div>

            <div class="col-md-5 m-auto">
              <div class="mb-3 text-center">
                <a
                  style="text-decoration: none"
                  class="cont-cont form-control d-flex align-item-center justify-content-center"
                  href="#"
                  ><i class="bx bxl-facebook-circle ico-face px-2"></i>Sign in
                  with Facebook</a
                >
              </div>
            </div>
            <div class="col-md-1 offset"></div>
          </div>

          <div class="row parent text-center">
            <div class="content m-auto">Or</div>
          </div>

          <div class="row py-3">
            <div class="col-md-10 m-auto">
              <label for="exampleFormControlInput1" class="form-label"
                >Email address</label
              >
              <input
                type="email"
                class="form-control pl-5"
                id="exampleFormControlInput1"
                placeholder="info.3x1@gmail.com"
              />
              <i
                style="
                  position: absolute;
                  top: 40px;
                  left: 29px;
                  font-size: 20px;
                "
                class="bx bx-mail-send"
              ></i>
            </div>
          </div>
          <div class="row py-3">
            <div class="col-md-10 m-auto">
              <label for="exampleFormControlInput1" class="form-label"
                >Password</label
              >
              <input
                type="password"
                class="form-control pl-5"
                id="exampleFormControlInput1"
                placeholder="..........."
              />
              <i
                style="position: absolute; top: 40px; left: 29px"
                class="bx bxs-lock"
              ></i>
            </div>
          </div>

          <div class="row">
            <div
              style="justify-content: space-between; display: flex"
              class="col-md-10 all-content-2 m-auto"
            >
              <div class="form-check">
                <!-- <div class="div-check"> -->
                <label class="form-check-label" for="flexCheckDefault">
                  Remember Me
                </label>
                <input
                  class="form-check-input"
                  type="checkbox"
                  value=""
                  id="flexCheckDefault"
                />
                <!-- </div> -->
              </div>
              <router-link to="/reset" style="color: #921e50">
                <div class="px-2">Forget Password?</div>
              </router-link>
            </div>
          </div>

          <div class="row py-2">
            <div class="col-md-10 m-auto">
              <button type="button" class="bt-login-2 btn-block btn btn-lg">
                Login
              </button>
            </div>
          </div>
        </section>

        <section class="text-center foot-er">
          <div class="container">
            <p class="py-3">Dont have an account?</p>
            <div class="row py-2">
              <div class="col-md-10 m-auto">
                <a href="./signup.html">
                  <button type="button" class="bt-login-5 btn-block btn btn-lg">
                    Sign Up
                  </button>
                </a>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>