# LMS UI Tests (Playwright)

ตรวจสอบความสูงปุ่ม ระยะห่าง layout และการทำงานของ UI ทั้งหน้า Public และ Admin

## ความต้องการ

- XAMPP / Apache รันอยู่ที่ `http://localhost/LMS/`
- บัญชี Admin: `admin` / `admin123` (หรือตั้งผ่าน env)

## ติดตั้ง

```bash
cd c:\xampp\htdocs\LMS
npm install
npx playwright install chromium
```

## รันเทสต์

```bash
npm test
```

รันแบบมี UI:

```bash
npm run test:ui
```

ดูรายงาน HTML:

```bash
npm run test:report
```

## ตัวแปรสภาพแวดล้อม (ไม่บังคับ)

| ตัวแปร | ค่าเริ่มต้น |
|--------|-------------|
| `LMS_BASE_URL` | `http://localhost/LMS/` |
| `LMS_ADMIN_USER` | `admin` |
| `LMS_ADMIN_PASS` | `admin123` |

## ไฟล์เทสต์

| ไฟล์ | ตรวจสอบ |
|------|---------|
| `ui/button-consistency.spec.js` | ความสูงปุ่มใน toolbar / filter bar / card header |
| `ui/layout-spacing.spec.js` | gap ของ grid และ flex บนแดชบอร์ดและหน้าคอร์ส |
| `ui/functionality.spec.js` | ติดต่อเรา, กรอง, ตัวกรองบทเรียน, modal คูปอง, โหลดหน้า admin |
