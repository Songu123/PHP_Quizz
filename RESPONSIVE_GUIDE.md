# 📱 QUIZMASTER - RESPONSIVE DESIGN GUIDE

## 🎯 Tổng quan

Giao diện QuizMaster được thiết kế hoàn toàn responsive, tối ưu cho mọi thiết bị từ mobile đến desktop.

---

## 📐 Breakpoints

### 1. **Extra Small Mobile** (< 375px)
- Font sizes giảm nhẹ
- Padding/margin tối thiểu
- Single column layout

### 2. **Small Mobile** (≥ 375px < 576px)
```css
@media (max-width: 576px) {
    body { padding-top: 60px; font-size: 14px; }
    .hero-title { font-size: 1.75rem; }
    .section-title { font-size: 1.5rem; }
}
```

### 3. **Mobile Landscape / Large Mobile** (≥ 576px < 768px)
- Improved spacing
- Larger touch targets
- Better typography

### 4. **Tablet Portrait** (≥ 768px < 992px)
```css
@media (max-width: 768px) {
    .hero-title { font-size: 2rem; }
    .hero-actions { flex-direction: column; width: 100%; }
    .quiz-cards-demo { display: none; }
}
```

### 5. **Tablet Landscape** (≥ 992px < 1200px)
```css
@media (max-width: 992px) {
    .hero-title { font-size: 2.5rem; }
    .quiz-cards-demo { height: 300px; }
    .user-stats { display: none !important; }
}
```

### 6. **Desktop** (≥ 1200px < 1400px)
- Standard layout
- All features visible
- Optimal spacing

### 7. **Large Desktop** (≥ 1400px)
- Maximum width containers
- Enhanced visual elements
- Premium spacing

---

## 🎨 Responsive Components

### **Navbar**
✅ **Desktop:** Horizontal menu với user stats
✅ **Tablet:** Collapsed menu, hidden stats
✅ **Mobile:** 
- Hamburger menu
- Full-width dropdown
- Touch-optimized links (44x44px minimum)
- Dark overlay background

```php
<!-- Mobile Menu -->
<nav class="navbar navbar-expand-lg">
    <button class="navbar-toggler"> <!-- Hamburger -->
    <div class="collapse navbar-collapse"> <!-- Responsive menu -->
```

### **Hero Section**
✅ **Desktop:** 2 columns (Text + Visual cards)
✅ **Tablet:** 2 columns, smaller cards
✅ **Mobile:** 
- Single column
- Hidden visual cards
- Full-width buttons
- Smaller emoji/title

```css
.hero-section {
    min-height: 100vh; /* Desktop */
}

@media (max-width: 768px) {
    .hero-section { 
        min-height: auto; 
        padding: 3rem 0 2rem;
    }
    .hero-visual { display: none; }
}
```

### **Stats Cards**
✅ **Desktop:** 4 columns (25% each)
✅ **Tablet:** 2 columns (50% each)
✅ **Mobile:** 1 column (100%)

```html
<div class="col-lg-3 col-md-6 mb-4">
    <div class="stat-card">...</div>
</div>
```

### **Feature Cards**
✅ **Desktop:** 3 columns (33.33% each)
✅ **Tablet:** 2 columns (50% each)
✅ **Mobile:** 1 column (100%)

```html
<div class="col-lg-4 col-md-6 mb-4">
    <div class="feature-card">...</div>
</div>
```

### **Subject Cards**
✅ Same grid as Feature Cards
✅ Smaller images on mobile
✅ Vertical stats layout on small screens

```css
@media (max-width: 768px) {
    .subject-image { height: 100px; font-size: 2rem; }
    .subject-stats { flex-direction: column; }
}
```

---

## 🖼️ Images & Media

### **Responsive Images**
```html
<img src="image.jpg" 
     srcset="image-sm.jpg 576w,
             image-md.jpg 768w,
             image-lg.jpg 992w,
             image-xl.jpg 1200w"
     sizes="(max-width: 576px) 100vw,
            (max-width: 768px) 50vw,
            33vw"
     alt="Description">
```

### **Background Images**
```css
.hero-section {
    background-image: url('hero-large.jpg');
}

@media (max-width: 768px) {
    .hero-section {
        background-image: url('hero-small.jpg');
    }
}
```

---

## 📝 Typography

### **Font Scaling**
```css
/* Desktop */
.hero-title { font-size: 3.5rem; }
.section-title { font-size: 2.5rem; }

/* Tablet */
@media (max-width: 992px) {
    .hero-title { font-size: 2.5rem; }
    .section-title { font-size: 2rem; }
}

/* Mobile */
@media (max-width: 576px) {
    .hero-title { font-size: 1.75rem; }
    .section-title { font-size: 1.5rem; }
    body { font-size: 14px; }
}
```

### **Line Heights**
- Desktop: `line-height: 1.6`
- Mobile: `line-height: 1.5` (tighter for small screens)

---

## 🎯 Touch Optimization

### **Button Sizes**
```css
/* Minimum 44x44px for touch targets */
.btn {
    padding: 0.75rem 1.5rem; /* Desktop */
}

@media (max-width: 576px) {
    .btn {
        padding: 0.875rem 1.5rem; /* Larger on mobile */
        width: 100%; /* Full width */
    }
}
```

### **Spacing**
```css
/* Touch-friendly spacing */
.navbar-nav .nav-link {
    padding: 0.5rem 1rem; /* Desktop */
}

@media (max-width: 992px) {
    .navbar-nav .nav-link {
        padding: 0.75rem 1rem; /* More space on mobile */
    }
}
```

---

## ⚡ Performance

### **Lazy Loading**
```html
<img src="placeholder.jpg" 
     data-src="actual-image.jpg" 
     loading="lazy" 
     alt="Description">
```

### **Conditional Loading**
```javascript
// Load heavy features only on desktop
if (window.innerWidth > 992) {
    initParticles();
    initAnimations();
}
```

### **Optimized Animations**
```css
/* Disable animations on mobile for better performance */
@media (max-width: 768px) {
    .quiz-card.floating {
        animation: none;
    }
}
```

---

## 🔍 Testing Checklist

### **Browser Testing**
- ✅ Chrome (Desktop, Mobile)
- ✅ Firefox (Desktop, Mobile)
- ✅ Safari (Desktop, iOS)
- ✅ Edge (Desktop)

### **Device Testing**
- ✅ iPhone SE (375x667)
- ✅ iPhone 12 Pro (390x844)
- ✅ iPad (768x1024)
- ✅ iPad Pro (1024x1366)
- ✅ Desktop (1920x1080)

### **Orientation Testing**
- ✅ Portrait mode
- ✅ Landscape mode

```css
@media (max-height: 600px) and (orientation: landscape) {
    .hero-section {
        min-height: auto;
        padding: 2rem 0;
    }
}
```

---

## 🛠️ Tools & Testing

### **Chrome DevTools**
1. Press `F12`
2. Click Toggle Device Toolbar (Ctrl+Shift+M)
3. Select device or enter custom dimensions
4. Test touch events, network throttling

### **Firefox Responsive Design Mode**
1. Press `Ctrl+Shift+M`
2. Select device presets
3. Test different DPR (Device Pixel Ratio)

### **Real Device Testing**
```
http://192.168.1.X/doan_mon/public
```
- Find your local IP: `ipconfig` (Windows) or `ifconfig` (Mac/Linux)
- Access from mobile device on same network
- Test touch, gestures, performance

### **Lighthouse Audit**
1. Open Chrome DevTools
2. Go to Lighthouse tab
3. Select Mobile/Desktop
4. Run audit
5. Check:
   - Performance: ≥ 90
   - Accessibility: ≥ 90
   - Best Practices: ≥ 90
   - SEO: ≥ 90

---

## 📱 Mobile-First Approach

### **Why Mobile-First?**
1. Simpler base styles
2. Progressive enhancement
3. Better performance on mobile
4. Forces prioritization of content

### **Implementation**
```css
/* Base styles (Mobile) */
.hero-title {
    font-size: 1.75rem;
}

/* Tablet and up */
@media (min-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
}

/* Desktop */
@media (min-width: 1200px) {
    .hero-title {
        font-size: 3.5rem;
    }
}
```

---

## 🎨 Best Practices

### **1. Use Relative Units**
```css
/* ❌ Bad */
.container { width: 1200px; }

/* ✅ Good */
.container { max-width: 100%; width: 1200px; }
```

### **2. Flexible Images**
```css
img {
    max-width: 100%;
    height: auto;
}
```

### **3. Touch-Friendly**
```css
/* Minimum 44x44px touch targets */
.btn, .nav-link {
    min-height: 44px;
    min-width: 44px;
}
```

### **4. Avoid Fixed Widths**
```css
/* ❌ Bad */
.card { width: 300px; }

/* ✅ Good */
.card { max-width: 300px; width: 100%; }
```

### **5. Stack on Mobile**
```css
/* Desktop: horizontal */
.hero-actions {
    display: flex;
    gap: 1rem;
}

/* Mobile: vertical */
@media (max-width: 768px) {
    .hero-actions {
        flex-direction: column;
        width: 100%;
    }
}
```

---

## 📊 Accessibility

### **ARIA Labels**
```html
<button class="navbar-toggler" 
        aria-label="Toggle navigation"
        aria-expanded="false">
    <span class="navbar-toggler-icon"></span>
</button>
```

### **Keyboard Navigation**
```css
/* Focus states */
.nav-link:focus,
.btn:focus {
    outline: 2px solid #667eea;
    outline-offset: 2px;
}
```

### **Semantic HTML**
```html
<nav> <!-- Navigation -->
<main> <!-- Main content -->
<aside> <!-- Sidebar -->
<footer> <!-- Footer -->
```

---

## 🚀 Quick Start

### **Test Responsive Design**
1. Open `http://localhost/doan_mon/public/responsive_test.html`
2. Resize browser window
3. Check all breakpoints
4. Test on real devices

### **Make Changes**
1. Edit `public/css/style.css`
2. Add media queries as needed
3. Test on multiple devices
4. Optimize performance

### **Deploy**
1. Minify CSS/JS
2. Optimize images
3. Enable caching
4. Test on production

---

## 📞 Support

Nếu gặp vấn đề về responsive:
1. Check console for errors
2. Verify viewport meta tag
3. Test in different browsers
4. Clear cache and reload

---

**Created with ❤️ by QuizMaster Team**
**Last Updated: 2025**
