
# Sylvstrr
This project was a team effort by Mason Pawsey, Zak Worman, Tyler Sharp, and Randi Al Ghrer. We were presented with the *Computer Science Crowd Favourite at Senior Design Expo* by Dr Melissa Danforth, Department Chair and Dr Kathleen Madden, Dean, School of Natural Sciences, Mathematics, and Engineering. Our project was supervised by Dr Albert Cruz.

## Personnel
Mason Pawsey - General development

Zak Worman - AI development

Tyler Sharp - General development

Randi Al Ghrer - Map development

## Purpose
Our goal was to quantify how people from a specific location felt about a specific subject. We accomplished this by analysing the sentiments of Tweets people published; we chose Tweets due to their availability (many thanks to [Seb Insua](https://github.com/sebinsua/scrape-twitter)), their candidness, and their brevity. 

### Limitations
Our AI, while more accurate than a coin toss (based on randomly-selected Tweets and their classifications), did come with some limitations.

##### Literary Devices
Twitter, perhaps one of the most sarcastic corners of the Internet, sees a lot of euphemism, hyperbole, irony, and satire. Unfortunately our AI was fairly literal, making it difficult to weigh these literary devices for their *"actual"* meaning.

##### Memes
![Image of Yaktocat](https://i.imgflip.com/3d8ot8.jpg)

##### Emojis
We (Zak) generated a corpus file with emojis to give positive and negative weights to emojis, but again, they're often used in complex ways that we couldn't train our AI to understand.

For example, we may know that `I'm dead 💀💀` just means that someone is having a right laugh; a computer, on the other hand might interpret that as the Tweeter is posting from the afterlife.

##### Language
As our team was only familiar with English and what we'll call "construction-site Spanish", we could only (somewhat) accurately train our AI to interpret English. Therefore, we limited our Tweet scraping to majority English-speaking cities.


## Outcomes
Here is a good example of the current "Twitter outrage" over [Blizzard's recent actions](https://old.reddit.com/r/OutOfTheLoop/comments/df244c/whats_up_with_blizzard_casters_being_fired_over/) trending in Los Angeles and in Beijing.
![](https://i.imgur.com/AgMO0QS.png)

Sylvstrr allows users to visually see trends of sentiment on a global level, for anything that people are Tweeting about.

Another example is during an Instagram outage:

![https://i.imgur.com/o00Dm1u.png](https://i.imgur.com/o00Dm1u.png)

versus normal Instagram operations:
![https://i.imgur.com/08SxmZc.png](https://i.imgur.com/08SxmZc.png)


The project is still live and under development by Mason Pawsey. The custom AI that was developed for this project was executed on servers at California State University, Bakersfield; however, the sentiment of Tweets is currently analyzed using [Azure's Text Analysis API](https://westcentralus.dev.cognitive.microsoft.com/docs/services/TextAnalytics-v2-1/operations/56f30ceeeda5650db055a3c9).

# To do

- [ ] Ability for users to edit their profile (avatar, common name instead of email, etc)

- [ ] Bootstrap responsiveness

- [ ] Share map as a picture to social media

- [ ] Export map with a watermark for sylvstrr + image data (timestamp + query log)

- [ ] Footer floats on mobile?

- [x] Text Twilio for account info

- [x] Store user image gallery

- [x] Export map as a high res image

- [x] Login button doesn't reset from :focus(?) when login fails

- [x] Delete account

- [x] Reset password function

- [x] 404 redirections

- [x] HTTPS

- [x] Favicon

- [x] 2FA

- [x] User stats page (more recent searches, most popular keyword & location)

- [x] User analytics

- [x] Recent searches not working with < 3 searches

- [x] Authenticate search.php

- [x] Track users searches

- [x] User accounts

- [x] Pull passwords out and store them in a `.gitignore` file
