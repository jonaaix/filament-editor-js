(function () {
    // Prevent double initialization
    if (window.AaixGalleryInitialized) return;
    window.AaixGalleryInitialized = true;

    class AaixGallery {
        constructor(wrapper) {
            this.wrapper = wrapper;
            try {
                this.images = JSON.parse(wrapper.dataset.aaixGallery || '[]');
                this.style = wrapper.dataset.style || 'fit';
            } catch (e) {
                console.error('AaixGallery: Invalid JSON data', e);
                return;
            }

            this.activeIndex = 0;
            this.init();
        }

        init() {
            // Render specific layout
            if (this.style === 'slider') {
                this.renderSlider();
            } else {
                this.renderMasonry();
            }

            // Init global lightbox if not exists
            AaixLightbox.init(this.wrapper.querySelector('.js-lightbox-template'));
        }

        renderMasonry() {
            const container = this.wrapper.querySelector('.js-gallery-masonry');
            if (!container) return;

            const fragment = document.createDocumentFragment();

            this.images.forEach((img, index) => {
                const item = document.createElement('div');
                item.className = 'gallery-masonry__item';
                item.addEventListener('click', () => AaixLightbox.open(this, index));

                const imageEl = document.createElement('img');
                imageEl.src = img._1k;
                imageEl.srcset = `${img._500} 500w, ${img._1k} 1000w`;
                imageEl.sizes = '(max-width: 600px) 50vw, 33vw';
                imageEl.loading = 'lazy';
                imageEl.alt = img.caption || '';

                // Add hover zoom effect logic if needed, currently CSS handles it

                item.appendChild(imageEl);

                if (img.caption) {
                    const overlay = document.createElement('div');
                    overlay.className = 'gallery-masonry__overlay';
                    const span = document.createElement('span');
                    span.textContent = img.caption;
                    overlay.appendChild(span);
                    item.appendChild(overlay);
                }

                fragment.appendChild(item);
            });

            container.appendChild(fragment);
        }

        renderSlider() {
            const container = this.wrapper.querySelector('.js-gallery-slider');
            if (!container) return;

            this.sliderStage = container.querySelector('.js-slider-stage');
            this.sliderImage = document.createElement('img');
            this.sliderImage.className = 'gallery-slider__image';
            this.sliderImage.addEventListener('click', () => AaixLightbox.open(this, this.activeIndex));

            // Insert Image into Stage (before controls)
            this.sliderStage.insertBefore(this.sliderImage, this.sliderStage.firstChild);

            this.sliderCaption = container.querySelector('.js-slider-caption');
            this.sliderThumbs = container.querySelector('.js-slider-thumbs');
            this.sliderControls = container.querySelector('.js-slider-controls');

            // Bind Controls
            container.querySelector('.js-prev')?.addEventListener('click', (e) => {
                e.stopPropagation();
                this.prev();
            });
            container.querySelector('.js-next')?.addEventListener('click', (e) => {
                e.stopPropagation();
                this.next();
            });

            // Render Thumbs
            if (this.images.length > 1) {
                this.sliderControls.style.display = 'flex';
                this.sliderThumbs.style.display = 'flex';

                this.images.forEach((img, index) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'gallery-slider__thumb';
                    btn.onclick = () => this.goTo(index);

                    const thumbImg = document.createElement('img');
                    thumbImg.src = img._500;
                    thumbImg.loading = 'lazy';

                    btn.appendChild(thumbImg);
                    this.sliderThumbs.appendChild(btn);
                });
            }

            this.updateSliderUI();
        }

        updateSliderUI() {
            const img = this.images[this.activeIndex];

            // Update Main Image
            this.sliderImage.style.opacity = '0';

            // Simple fade transition
            requestAnimationFrame(() => {
                this.sliderImage.src = img._2k;
                this.sliderImage.srcset = `${img._1k} 1000w, ${img._2k} 2000w, ${img._3k} 3000w, ${img._4k} 4000w`;
                this.sliderImage.alt = img.caption;
                this.sliderImage.classList.toggle('is-landscape', (img.width > img.height));

                this.sliderImage.onload = () => {
                    this.sliderImage.style.opacity = '1';
                };
            });

            // Update Caption
            if (img.caption) {
                this.sliderCaption.textContent = img.caption;
                this.sliderCaption.style.display = 'block';
            } else {
                this.sliderCaption.style.display = 'none';
            }

            // Update Thumbs Active State
            if (this.sliderThumbs.children.length) {
                Array.from(this.sliderThumbs.children).forEach((btn, idx) => {
                    btn.classList.toggle('is-active', idx === this.activeIndex);
                });

                // Auto scroll thumbs
                const activeBtn = this.sliderThumbs.children[this.activeIndex];
                if (activeBtn) {
                    activeBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }
            }
        }

        next() {
            this.activeIndex = (this.activeIndex + 1) % this.images.length;
            this.updateSliderUI();
        }

        prev() {
            this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length;
            this.updateSliderUI();
        }

        goTo(index) {
            this.activeIndex = index;
            this.updateSliderUI();
        }
    }

    // Singleton Lightbox Manager
    const AaixLightbox = {
        el: null,
        img: null,
        caption: null,
        download: null,
        currentGallery: null,
        currentIndex: 0,

        init(templateElement) {
            if (document.getElementById('aaix-gallery-lightbox')) {
                this.bindElements();
                return;
            }

            if (!templateElement) return;

            // Extract content from template and append to body
            const content = templateElement.content.cloneNode(true);
            document.body.appendChild(content);
            this.bindElements();
        },

        bindElements() {
            this.el = document.getElementById('aaix-gallery-lightbox');
            if (!this.el) return;

            this.img = this.el.querySelector('.js-lightbox-img');
            this.caption = this.el.querySelector('.js-lightbox-caption');
            this.download = this.el.querySelector('.js-lightbox-download');

            // Event Listeners
            this.el.querySelectorAll('.js-lightbox-close').forEach(el => {
                el.addEventListener('click', (e) => {
                    if (e.target !== e.currentTarget) return; // check bubbling
                    this.close();
                });
            });

            this.el.querySelector('.js-lightbox-next')?.addEventListener('click', (e) => {
                e.stopPropagation();
                this.next();
            });

            this.el.querySelector('.js-lightbox-prev')?.addEventListener('click', (e) => {
                e.stopPropagation();
                this.prev();
            });

            // Keyboard Nav
            document.addEventListener('keydown', (e) => {
                if (this.el.style.display === 'none') return;
                if (e.key === 'Escape') this.close();
                if (e.key === 'ArrowRight') this.next();
                if (e.key === 'ArrowLeft') this.prev();
            });
        },

        open(galleryInstance, index) {
            if (!this.el) return;

            this.currentGallery = galleryInstance;
            this.currentIndex = index;

            this.updateUI();

            this.el.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Lock scroll

            // Fade in
            requestAnimationFrame(() => {
                this.el.style.opacity = '1';
            });
        },

        close() {
            if (!this.el) return;
            this.el.style.opacity = '0';
            setTimeout(() => {
                this.el.style.display = 'none';
                document.body.style.overflow = '';
                this.currentGallery = null;
            }, 200); // Match CSS transition duration
        },

        next() {
            if (!this.currentGallery) return;
            this.currentIndex = (this.currentIndex + 1) % this.currentGallery.images.length;
            this.updateUI();
        },

        prev() {
            if (!this.currentGallery) return;
            this.currentIndex = (this.currentIndex - 1 + this.currentGallery.images.length) % this.currentGallery.images.length;
            this.updateUI();
        },

        updateUI() {
            if (!this.currentGallery) return;
            const data = this.currentGallery.images[this.currentIndex];

            // 1. Image
            this.img.src = data._2k;
            this.img.srcset = `${data._1k} 1000w, ${data._2k} 2000w, ${data._3k} 3000w, ${data._4k} 4000w`;

            // 2. Caption
            if (data.caption) {
                this.caption.textContent = data.caption;
                this.caption.style.display = 'block';
            } else {
                this.caption.style.display = 'none';
            }

            // 3. Download Link
            if (this.download) {
                this.download.href = data._original;
            }

            // 4. Arrows visibility
            const hasMultiple = this.currentGallery.images.length > 1;
            this.el.querySelector('.js-lightbox-next').style.display = hasMultiple ? 'block' : 'none';
            this.el.querySelector('.js-lightbox-prev').style.display = hasMultiple ? 'block' : 'none';
        }
    };

    // Auto-Init on Load
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.aaix-gallery-block').forEach(el => new AaixGallery(el));
    });

})();
