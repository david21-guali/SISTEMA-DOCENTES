<div id="loader-wrapper" style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    transition: opacity 0.5s ease;
">
    <div class="loader"></div>
</div>

<style>
.loader {
 position: relative;
 width: 2.5em;
 height: 2.5em;
 transform: rotate(165deg);
}

.loader:before, .loader:after {
 content: "";
 position: absolute;
 top: 50%;
 left: 50%;
 display: block;
 width: 0.5em;
 height: 0.5em;
 border-radius: 0.25em;
 transform: translate(-50%, -50%);
}

.loader:before {
 animation: before8 2s infinite;
}

.loader:after {
 animation: after6 2s infinite;
}

@keyframes before8 {
 0% {
  width: 0.5em;
  box-shadow: 1em -0.5em rgba(225, 20, 98, 0.75), -1em 0.5em rgba(111, 202, 220, 0.75);
 }

 35% {
  width: 2.5em;
  box-shadow: 0 -0.5em rgba(225, 20, 98, 0.75), 0 0.5em rgba(111, 202, 220, 0.75);
 }

 70% {
  width: 0.5em;
  box-shadow: -1em -0.5em rgba(225, 20, 98, 0.75), 1em 0.5em rgba(111, 202, 220, 0.75);
 }

 100% {
  box-shadow: 1em -0.5em rgba(225, 20, 98, 0.75), -1em 0.5em rgba(111, 202, 220, 0.75);
 }
}

@keyframes after6 {
 0% {
  height: 0.5em;
  box-shadow: 0.5em 1em rgba(61, 184, 143, 0.75), -0.5em -1em rgba(233, 169, 32, 0.75);
 }

 35% {
  height: 2.5em;
  box-shadow: 0.5em 0 rgba(61, 184, 143, 0.75), -0.5em 0 rgba(233, 169, 32, 0.75);
 }

 70% {
  height: 0.5em;
  box-shadow: 0.5em -1em rgba(61, 184, 143, 0.75), -0.5em 1em rgba(233, 169, 32, 0.75);
 }

 100% {
  box-shadow: 0.5em 1em rgba(61, 184, 143, 0.75), -0.5em -1em rgba(233, 169, 32, 0.75);
 }
}
</style>

<script>
    window.addEventListener('load', function() {
        const loader = document.getElementById('loader-wrapper');
        if (loader) {
            setTimeout(() => {
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 500);
            }, 300); // Small delay to enjoy the loader
        }
    });
</script>
