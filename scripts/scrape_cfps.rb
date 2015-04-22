#!/usr/bin/ruby

require 'rubygems'
require 'nokogiri'
require 'open-uri'
require 'sqlite3'

$script_dir = File.dirname(__FILE__)

# Read in categories names from a text file (one per line)
def load_categories(file)
  categories = IO.readlines(file)

  categories.each do |cat|
    cat.strip!
  end

  return categories
end

# Return the id of a category in the database given its name
def get_category_id(category)
  db = SQLite3::Database.new "#{$script_dir}/cfps.db"
  category_id = db.get_first_value("select id from category where name = ?", [category])

  return category_id
end

# Return the name of a category in the database given its id
def get_category_name(category_id)
  db = SQLite3::Database.new "#{$script_dir}/cfps.db"
  category = db.get_first_value("select name from category where id = ?", [category_id])

  return categor
end

# Fetch the HTML containing list of CFPs for a category 
def scrape_cfp_list(category)
  category_encoded = category.sub(' ','%20')
  content = ''
  page = 1

  until (content =~ /Expired/)
    uri = "http://www.wikicfp.com/cfp/call?conference=#{category_encoded}&page=#{page}"
    body = (Nokogiri::HTML(open(uri))).to_s
        
    content << body
    page += 1
  end

  content = content.split(/Expired/).first
  return content
end

# Extract links for each individual CFP (from HTML containing list of CFPs)
def scrape_cfp_links(content)
  page = Nokogiri::HTML(content)

  event_links = Array.new

  links = page.css("a")
  links.each do |a|
    if a["href"].include? "showcfp"
      event_links.push "http://wikicfp.com#{a["href"]}"
    end
  end

  return event_links
end

# Scrapes info about a particular CFP and stores it in the database
def scrape_and_store_cfp_info(category_id, link)
  uri = link
  page = Nokogiri::HTML(open(uri))

  event = page.css('title').text

  headers = Array.new
  data = Array.new

  # Get Table Headers
  page.xpath('//tr/th').each do |e|
    headers.push(e.text.to_s)
  end


  # Get Table Data
  page.xpath('//tr/th/following-sibling::*').each do |d|
    data.push(d.text.strip)
  end

  hash = Hash[headers.zip(data)]

  event_name = event[/(.*?):/,1].strip
  event_full_name = (event[/:(.*)/,1] || event_name).strip

  event_date = (hash["When"] || "N/A")
  event_location = (hash["Where"] || "N/A")

  abstract_due = (hash["Abstract Registration Due"] || "N/A")
  submission_due = (hash["Submission Deadline"] || "N/A")
  notification_due = (hash["Notification Due"] || "N/A")
  final_due = (hash["Final Version Due"] || "N/A")

  official_link = (page.to_s[/Link:\s*<a\shref="(.*?)"/,1] || "N/A").strip
  wikicfp_link = uri

  db = SQLite3::Database.new "#{$script_dir}/cfps.db"
  db.execute("INSERT INTO event (id, category_id, name, full_name, date, location, abstract_due, submission_due, notification_due, final_due, wikicfp_link, official_link)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
             [nil, category_id, event_name, event_full_name, event_date, event_location, abstract_due, submission_due, notification_due, final_due, wikicfp_link, official_link])

end


# Main
puts "Script dir: #{$script_dir}\n"
categories = load_categories("#{$script_dir}/categories_full.txt")

categories.each do |cat|
  puts "Processing category: #{cat}\n"

  category_id = get_category_id(cat)

  content = scrape_cfp_list(cat)
  links = scrape_cfp_links(content)

  links.each do |link|
    scrape_and_store_cfp_info(category_id, link)
  end
end
