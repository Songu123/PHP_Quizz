from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
import time

# Khởi tạo trình duyệt Chrome
driver = webdriver.Chrome()

# Mở Google
driver.get("https://www.google.com")

# Tìm ô tìm kiếm theo tên
search_box = driver.find_element(By.NAME, "q")

# Nhập từ khóa và nhấn Enter
search_box.send_keys("Selenium Python")
search_box.send_keys(Keys.RETURN)

time.sleep(3)  # Chờ 3 giây để thấy kết quả

# Kiểm tra tiêu đề trang
assert "Python" in driver.title

# Đóng trình duyệt
driver.quit()
