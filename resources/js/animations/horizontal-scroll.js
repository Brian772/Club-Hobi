import gsap from "gsap";
import ScrollTrigger from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

let horizontalMatchMedia;

function getScrollDistance(track, viewport) {
    return Math.max(0, track.scrollWidth - viewport.clientWidth);
}

function waitForImages(container) {
    const imgs = Array.from(container.querySelectorAll("img"));
    return Promise.all(
        imgs.map((img) =>
            img.complete
                ? Promise.resolve()
                : new Promise((resolve) => {
                      img.addEventListener("load", resolve);
                      img.addEventListener("error", resolve);
                  }),
        ),
    );
}

async function initHorizontalScroll() {
    const track = document.querySelector(".horizontal-track");
    const section = document.querySelector(".horizontal-section");
    const viewport = document.querySelector(".horizontal-scroll-viewport");

    if (!track || !section || !viewport) return;

    await waitForImages(track);

    horizontalMatchMedia?.revert();

    const mm = gsap.matchMedia();
    horizontalMatchMedia = mm;

    mm.add("(min-width: 768px)", () => {
        if (getScrollDistance(track, viewport) <= 0) {
            return () => gsap.set(track, { clearProps: "transform" });
        }

        const tween = gsap.to(track, {
            x: () => -getScrollDistance(track, viewport),
            ease: "none",
            scrollTrigger: {
                trigger: section,
                start: "top top",
                end: () => "+=" + getScrollDistance(track, viewport),
                scrub: 1,
                pin: true,
                anticipatePin: 1,
                invalidateOnRefresh: true,
            },
        });

        return () => {
            tween.scrollTrigger?.kill();
            tween.kill();
            gsap.set(track, { clearProps: "transform" });
        };
    });
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initHorizontalScroll);
} else {
    initHorizontalScroll();
}

window.addEventListener("resize", () => {
    ScrollTrigger.refresh();
});

let resizeTimer;
window.addEventListener("resize", () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => ScrollTrigger.refresh(), 200);
});