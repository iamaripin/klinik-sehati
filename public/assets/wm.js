window.CWModal = (function () {

    let zIndex = 2000;

    function create(options = {}) {

        const width = options.width || 400;
        const height = options.height || 300;
        const title = options.title || "Window";

        const win = document.createElement("div");
        win.classList.add("cw-window");
        win.style.width = width + "px";
        win.style.height = height + "px";
        win.style.zIndex = ++zIndex;

        /* HEADER */
        const header = document.createElement("div");
        header.classList.add("cw-header");
        header.innerHTML = `
        <div class="cw-title">${title}</div>
        <div class="cw-controls">
            <button class="cw-min"></button>
            <button class="cw-max"></button>
            <button class="cw-close"></button>
        </div>
    `;

        /* BODY */
        const body = document.createElement("div");
        body.classList.add("cw-body");

        if (options.content instanceof HTMLElement) {
            body.appendChild(options.content);
        } else {
            body.innerHTML = options.content || "";
        }

        /* RESIZER */
        const resizer = document.createElement("div");
        resizer.classList.add("cw-resizer");

        win.appendChild(header);
        win.appendChild(body);
        win.appendChild(resizer);

        document.body.appendChild(win);

        setInitialPosition(win, options);
        activate(win);
        makeDraggable(win);
        makeResizable(win);

        win.addEventListener("mousedown", () => activate(win));

        /* BUTTON EVENTS */
        win.querySelector(".cw-close").onclick = () => win.remove();

        win.querySelector(".cw-min").onclick = () => {
            win.classList.toggle("cw-minimized");
        };

        win.querySelector(".cw-max").onclick = () => {

            if (win.classList.contains("cw-maximized")) {
                win.classList.remove("cw-maximized");
            } else {
                win.classList.remove("cw-minimized");
                win.classList.add("cw-maximized");
            }
        };

        return win;
    }

    /* ================= POSITION ================= */

    function setInitialPosition(win, options) {

        const margin = 20;

        if (options.position) {

            switch (options.position) {

                case "top-left":
                    win.style.top = margin + "px";
                    win.style.left = margin + "px";
                    break;

                case "top-right":
                    win.style.top = margin + "px";
                    win.style.left = (window.innerWidth - win.offsetWidth - margin) + "px";
                    break;

                case "bottom-left":
                    win.style.top = (window.innerHeight - win.offsetHeight - margin) + "px";
                    win.style.left = margin + "px";
                    break;

                case "bottom-right":
                    win.style.top = (window.innerHeight - win.offsetHeight - margin) + "px";
                    win.style.left = (window.innerWidth - win.offsetWidth - margin) + "px";
                    break;

                case "center":
                default:
                    win.style.top = (window.innerHeight / 2 - win.offsetHeight / 2) + "px";
                    win.style.left = (window.innerWidth / 2 - win.offsetWidth / 2) + "px";
                    break;
            }

        } else {
            win.style.top = options.top || "100px";
            win.style.left = options.left || "100px";
        }
    }

    /* ================= ACTIVATE ================= */

    function activate(win) {

        document.querySelectorAll(".cw-window")
            .forEach(w => w.classList.remove("cw-active"));

        win.classList.add("cw-active");
        win.style.zIndex = ++zIndex;
    }

    /* ================= DRAG ================= */

    function makeDraggable(win) {

        const header = win.querySelector(".cw-header");
        let offsetX, offsetY, dragging = false;

        header.addEventListener("mousedown", (e) => {

            if (win.classList.contains("cw-maximized")) return;

            dragging = true;
            offsetX = e.clientX - win.offsetLeft;
            offsetY = e.clientY - win.offsetTop;

            document.addEventListener("mousemove", drag);
            document.addEventListener("mouseup", stopDrag);
        });

        function drag(e) {

            if (!dragging) return;

            let newLeft = e.clientX - offsetX;
            let newTop = e.clientY - offsetY;

            const maxLeft = window.innerWidth - win.offsetWidth;
            const maxTop = window.innerHeight - win.offsetHeight;

            win.style.left = Math.max(0, Math.min(newLeft, maxLeft)) + "px";
            win.style.top = Math.max(0, Math.min(newTop, maxTop)) + "px";
        }

        function stopDrag() {
            dragging = false;
            document.removeEventListener("mousemove", drag);
            document.removeEventListener("mouseup", stopDrag);
        }
    }

    /* ================= RESIZE ================= */

    function makeResizable(win) {

        const resizer = win.querySelector(".cw-resizer");
        let resizing = false;

        resizer.addEventListener("mousedown", (e) => {
            e.stopPropagation();
            resizing = true;
            document.addEventListener("mousemove", resize);
            document.addEventListener("mouseup", stopResize);
        });

        function resize(e) {

            if (!resizing) return;

            let newWidth = e.clientX - win.offsetLeft;
            let newHeight = e.clientY - win.offsetTop;

            const maxWidth = window.innerWidth - win.offsetLeft;
            const maxHeight = window.innerHeight - win.offsetTop;

            win.style.width = Math.min(newWidth, maxWidth) + "px";
            win.style.height = Math.min(newHeight, maxHeight) + "px";
        }

        function stopResize() {
            resizing = false;
            document.removeEventListener("mousemove", resize);
            document.removeEventListener("mouseup", stopResize);
        }
    }

    return { create };

})();
