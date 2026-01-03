# ✅ Blog Archive Mobile - 1:1 z Figmy

_Dokładnie zaimplementowany design mobilny_

---

## 📐 WYMIARY (dokładnie z Figmy)

### Kontener główny:
- Width: **343px** (91.4667vw / 375px)
- Margin-left: **8px** (2.1333vw)
- Gap między elementami: **16px** (4.2667vw)

### Separatory:
- Height: **0.5px** (0.1333vw)
- Background: **#e3ebec**
- Między każdym postem

### Regular Post (poziomo):
- **Image:** 74px x 56px, border-radius: 2px
- **Content:** 237px wide
- **Gap:** 16px między image a content
- **Title:** 
  - Font: Nohemi, 16px, weight: 300
  - Line-height: 24px
  - Color: #404848
  - Single line z ellipsis
  - Min-height: 35px (dla 1-2 linii)
- **Meta:** 
  - Gap: 12px przed text
  - Font: DM Mono, 10px, weight: 300
  - Line-height: 7px
  - Color: #6f7b7c
  - Letter-spacing: 0.5px
  - Uppercase, white-space: nowrap
- **Gap między title a meta:** 14px

### Large Post (prostokąt szary):
- Width: **359px** (95.7333vw)
- Background: **#e3ebec**
- Border-radius: **8px**
- **Image:** 342px x 254px, margin: 8px 0 0 9px
- **Title:** 
  - Width: 343px, margin: 32px 0 0 8px
  - Font: Nohemi, 26px, weight: 300
  - Line-height: 31.2px
  - Może mieć więcej linii
- **Excerpt:**
  - Container: width: 343px, margin: 32px 0 0 8px
  - Inner: padding: 0 0 0 16px, border-left: 4px solid #6f7b7c
  - Text: 311px wide, 16px, line-height: 24px
  - Ellipsis overflow
- **Meta:**
  - Width: 295px, margin: 38px 0 0 8px
  - Avatar: 32px circle
  - Text: 12px, line-height: 8px
  - Letter-spacing: -0.24px (specyficzne!)

### Pagination:
- Width: **253px** (67.4667vw)
- Margin: **64px 0 0 98px** (17.0667vw 0 0 26.1333vw)
- Gap: **42px** między numbers a arrow
- **Numbers:**
  - Gap: 16px
  - Font: DM Mono, 12px, weight: 300
  - Line-height: 8px
  - Letter-spacing: 0.6px
  - Active: #404848, Inactive: #a3afb0
- **Separator:** 39px x 1px
- **Arrow:** 48px x 32px (obrazek, nie SVG w tym przypadku)

---

## ✅ CO ZOSTAŁO ZAIMPLEMENTOWANE

### CSS - dokładnie 1:1:
```css
/* Kontener */
.kpg-post-list {
  width: 91.4667vw; /* 343px */
  margin: 0 0 0 2.1333vw; /* 0 0 0 8px */
  gap: 4.2667vw; /* 16px */
}

/* Separator */
.kpg-post-separator {
  height: 0.1333vw; /* 0.5px */
  background: #e3ebec;
}

/* Regular Post */
.kpg-post-list-item {
  width: 87.2vw; /* 327px */
  gap: 4.2667vw; /* 16px */
}

.kpg-post-list-item-image {
  width: 19.7333vw; /* 74px */
  height: 14.9333vw; /* 56px */
  border-radius: 0.5333vw; /* 2px */
}

.kpg-post-list-item-content {
  width: 63.2vw; /* 237px */
  gap: 3.7333vw; /* 14px */
}

.kpg-post-list-item-title {
  font-size: 4.2667vw; /* 16px */
  line-height: 6.4vw; /* 24px */
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.kpg-post-list-item-author-date {
  font-size: 2.6667vw; /* 10px */
  line-height: 1.8667vw; /* 7px */
  letter-spacing: 0.1333vw; /* 0.5px */
}

/* Large Post */
.kpg-post-large {
  width: 95.7333vw; /* 359px */
  background: #e3ebec;
  border-radius: 2.1333vw; /* 8px */
}

.kpg-post-large-image {
  width: 91.2vw; /* 342px */
  height: 67.7333vw; /* 254px */
  margin: 2.1333vw 0 0 2.4vw; /* 8px 0 0 9px */
}

.kpg-post-large-title {
  width: 91.4667vw; /* 343px */
  margin: 8.5333vw 0 0 2.1333vw; /* 32px 0 0 8px */
  font-size: 6.9333vw; /* 26px */
  line-height: 8.32vw; /* 31.2px */
}

.kpg-post-large-excerpt-wrapper {
  width: 91.4667vw; /* 343px */
  margin: 8.5333vw 0 0 2.1333vw; /* 32px 0 0 8px */
  padding: 0 0 0 4.2667vw; /* 0 0 0 16px */
}

.kpg-post-large-excerpt-inner {
  width: 87.2vw; /* 327px */
  padding: 0 0 0 4.2667vw; /* 0 0 0 16px */
  border-left: 1.0667vw solid #6f7b7c; /* 4px */
}

.kpg-post-large-excerpt {
  width: 82.9333vw; /* 311px */
  font-size: 4.2667vw; /* 16px */
  line-height: 6.4vw; /* 24px */
}

.kpg-post-large-meta {
  width: 78.6667vw; /* 295px */
  margin: 10.1333vw 0 0 2.1333vw; /* 38px 0 0 8px */
  gap: 3.2vw; /* 12px */
}

.kpg-post-large-avatar {
  width: 8.5333vw; /* 32px */
  height: 8.5333vw; /* 32px */
}

.kpg-post-large-author-date {
  font-size: 3.2vw; /* 12px */
  line-height: 2.1333vw; /* 8px */
  letter-spacing: -0.064vw; /* -0.24px */
}

/* Pagination */
.kpg-blog-pagination {
  margin: 17.0667vw 0 0 26.1333vw; /* 64px 0 0 98px */
  gap: 11.2vw; /* 42px */
  width: 67.4667vw; /* 253px */
}

.kpg-blog-pagination-numbers {
  gap: 4.2667vw; /* 16px */
  width: 43.4667vw; /* 163px */
}

.kpg-blog-pagination-item {
  height: 2.1333vw; /* 8px */
  font-size: 3.2vw; /* 12px */
  line-height: 2.1333vw; /* 8px */
  letter-spacing: 0.16vw; /* 0.6px */
  color: #a3afb0; /* inactive */
}

.kpg-blog-pagination-item.active {
  color: #404848; /* active */
}

.kpg-blog-pagination-separator {
  width: 10.4vw; /* 39px */
  height: 0.2667vw; /* 1px */
}

.kpg-blog-pagination-arrow {
  width: 12.8vw; /* 48px */
  height: 8.5333vw; /* 32px */
}
```

---

## 🎯 WSZYSTKO 1:1 Z FIGMY

✅ Dokładne wymiary  
✅ Dokładne marginsy  
✅ Dokładne gaps  
✅ Dokładne fonty  
✅ Separatory między postami  
✅ Large post z gray background  
✅ Pagination z dokładnym margin  

**Gotowe do testowania!** 🚀



