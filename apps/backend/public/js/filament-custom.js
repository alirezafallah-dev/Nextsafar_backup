/**
 * انتقال تیتر صفحه از body به topbar (مطابق تم وردپرس)
 */
(function () {
    'use strict';
    var busy = false;

    function moveTitle() {
        if (busy) return;
        busy = true;
        try {
            var bodyHeader = document.querySelector('.fi-body > .fi-header');
            var topbar = document.querySelector('.fi-topbar');
            if (!bodyHeader || !topbar) return;

            if (topbar.querySelector('.ns-page-title')) {
                bodyHeader.style.display = 'none';
                return;
            }

            var heading = bodyHeader.querySelector('.fi-header-heading');
            var subheading = bodyHeader.querySelector('.fi-header-subheading');
            if (!heading) return;

            var wrap = document.createElement('div');
            wrap.className = 'ns-title-wrap';
            wrap.style.cssText = 'flex:1;display:flex;flex-direction:column;justify-content:center;';

            var h1 = document.createElement('h1');
            h1.className = 'ns-page-title';
            h1.textContent = heading.textContent.trim();
            wrap.appendChild(h1);

            if (subheading) {
                var p = document.createElement('p');
                p.className = 'ns-page-subtitle';
                p.textContent = subheading.textContent.trim();
                wrap.appendChild(p);
            }

            var anchor = topbar.querySelector('.fi-topbar-start') || topbar;
            anchor.insertBefore(wrap, anchor.firstChild);
            bodyHeader.style.display = 'none';
        } catch (e) {
            console.error('moveTitle:', e);
        } finally {
            setTimeout(function () { busy = false; }, 100);
        }
    }

    setTimeout(moveTitle, 300);
    document.addEventListener('livewire:navigated', function () { setTimeout(moveTitle, 200); });

    new MutationObserver(function () {
        var bh = document.querySelector('.fi-body > .fi-header');
        var tb = document.querySelector('.fi-topbar');
        if (bh && tb && !tb.querySelector('.ns-page-title')) moveTitle();
    }).observe(document.body, { childList: true, subtree: true });
})();
