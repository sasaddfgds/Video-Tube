import os
import urllib.request
import sqlite3
import datetime

# Create directories if they don't exist
os.makedirs('uploads/videos', exist_ok=True)
os.makedirs('uploads/posters', exist_ok=True)

# Sample video URLs (different formats, resolutions)
# We will use Big Buck Bunny and other standard test videos
videos_to_download = [
    # MP4 (H.264)
    ("https://test-videos.co.uk/vids/bigbuckbunny/mp4/h264/1080/Big_Buck_Bunny_1080_10s_1MB.mp4", "1080p", "mp4"),
    ("https://test-videos.co.uk/vids/bigbuckbunny/mp4/h264/720/Big_Buck_Bunny_720_10s_1MB.mp4", "720p", "mp4"),
    ("https://test-videos.co.uk/vids/bigbuckbunny/mp4/h264/360/Big_Buck_Bunny_360_10s_1MB.mp4", "360p", "mp4"),
    
    # WEBM (VP8/VP9)
    ("https://test-videos.co.uk/vids/bigbuckbunny/webm/vp8/1080/Big_Buck_Bunny_1080_10s_1MB.webm", "1080p", "webm"),
    ("https://test-videos.co.uk/vids/bigbuckbunny/webm/vp8/720/Big_Buck_Bunny_720_10s_1MB.webm", "720p", "webm"),
    ("https://test-videos.co.uk/vids/bigbuckbunny/webm/vp8/360/Big_Buck_Bunny_360_10s_1MB.webm", "360p", "webm"),
]

# We need 20 videos. Let's create variations in DB.
# For simplicity and speed, we will download the 6 base files and copy them to make 20 files.

base_files = []
print("Downloading base files...")
for url, res, ext in videos_to_download:
    filename = url.split('/')[-1]
    filepath = os.path.join('uploads/videos', filename)
    if not os.path.exists(filepath):
        try:
            urllib.request.urlretrieve(url, filepath)
            print(f"Downloaded {filename}")
        except Exception as e:
            print(f"Failed to download {url}: {e}")
            # Create a dummy file if download fails
            with open(filepath, 'wb') as f:
                f.write(b'dummy')
    base_files.append((filepath, res, ext))

# Download a dummy poster
poster_url = "https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Big_buck_bunny_poster_big.jpg/320px-Big_buck_bunny_poster_big.jpg"
poster_path = "uploads/posters/test_poster.jpg"
if not os.path.exists(poster_path):
    try:
        urllib.request.urlretrieve(poster_url, poster_path)
    except:
        with open(poster_path, 'wb') as f:
            f.write(b'dummy')

# Connect to DB
conn = sqlite3.connect('database.sqlite')
c = conn.cursor()

# Create dummy user if not exists
c.execute("INSERT OR IGNORE INTO users (id, username, password) VALUES (1, 'testuser', 'testpass')")
conn.commit()

# Generate 20 videos
print("Generating 20 test videos...")
for i in range(1, 21):
    base_file, res, ext = base_files[i % len(base_files)]
    new_filename = f"test_video_{i}_{res}.{ext}"
    new_filepath = os.path.join('uploads/videos', new_filename)
    
    # Copy file
    with open(base_file, 'rb') as f_src:
        with open(new_filepath, 'wb') as f_dst:
            f_dst.write(f_src.read())
            
    title = f"Test Video {i} - {res} ({ext})"
    description = f"This is an automated test video. Format: {ext}, Resolution: {res}."
    
    # Insert into DB
    c.execute("INSERT INTO videos (user_id, title, description, filename, poster) VALUES (?, ?, ?, ?, ?)",
              (1, title, description, new_filename, "test_poster.jpg"))

conn.commit()
conn.close()
print("Successfully generated 20 test videos and added to database.")
