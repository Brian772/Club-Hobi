import gsap from "gsap";
import ScrollTrigger from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

function initScaleScroll(selector = ".scale-card") {
    const cards = gsap.utils.toArray(selector);

    cards.forEach((card) => {
        gsap.fromTo(
            card,
            { scale: 0.6, opacity: 0.2 },
            {
                scale: 1,
                opacity: 1,
                ease: "none",
                scrollTrigger: {
                    trigger: card,
                    start: "top 80%",
                    end: "top 95%",
                    scrub: 1,
                },
            },
        );
    });
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", () => initScaleScroll());
} else {
  initScaleScroll();
}

window.addEventListener("resize", () => {
  ScrollTrigger.refresh();
});
