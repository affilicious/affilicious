require 'compass/import-once/activate'
# Require any additional compass plugins here.

# Set this to the root of your project when deployed:
http_path = "./assets"
css_dir = "./assets/css"
sass_dir = "./assets/scss"
images_dir = "./assets/images"
javascripts_dir = "./assets/js"

# You can select your preferred output style here (can be overridden via the command line):
#output_style = :compressed

# To enable relative paths to assets via compass helper functions. Uncomment:
relative_assets = true

# To disable debugging comments that display the original location of your selectors. Uncomment:
line_comments = false

sourcemap = true

sass_options = {:cache_location => "./assets/.cache"}