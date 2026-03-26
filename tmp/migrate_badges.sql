-- SomaTrack - Tiered Badges Migration
-- Nature and Minerals Theme

-- 1. Update existing badge names and icons to match the new theme
UPDATE badges SET name = 'Bronze Runner', icon = 'bi-person-walking' WHERE id = 1; -- Commitment (1)
UPDATE badges SET name = 'Busy Bee', icon = 'bi-bug' WHERE id = 2; -- Diligent (1)
UPDATE badges SET name = 'First Hill', icon = 'bi-tree' WHERE id = 3; -- Journeys (1)
UPDATE badges SET name = 'Popular Path', icon = 'bi-people' WHERE id = 4; -- Clones (5)
UPDATE badges SET name = 'Friendly Dolphin', icon = 'bi-water' WHERE id = 5; -- Interactions (10)
UPDATE badges SET name = '7-Day Spark', icon = 'bi-lightning-charge' WHERE id = 6; -- Streak (7)

-- 2. Add new Criteria Type to ENUM (if not already there)
-- Already checked, 'committed', 'diligent', 'community_helper', 'consistent' are all there.
-- Let's add 'aha_votes_received' just in case we need it for Level 3 Impact.
ALTER TABLE badges MODIFY COLUMN criteria_type ENUM('streak','journeys_completed','steps_completed','clones','aha_votes','committed','diligent','community_helper','consistent','aha_votes_received') NOT NULL;

-- 3. Insert Level 2 and Level 3 Badges

-- Commitment (Time)
INSERT IGNORE INTO badges (name, description, icon, criteria_type, criteria_value) VALUES 
('Silver Sprinter', 'Completed 5 learning journeys within their total estimated duration.', 'bi-speedometer', 'committed', 5),
('Gold Falcon', 'Completed 10 learning journeys within their total estimated duration.', 'bi-eye', 'committed', 10);

-- Consistency (Streak)
INSERT IGNORE INTO badges (name, description, icon, criteria_type, criteria_value) VALUES 
('30-Day Flame', 'Maintained a consistent 30-day learning streak.', 'bi-fire', 'consistent', 30),
('100-Day Sun', 'Maintained a consistent 100-day learning streak.', 'bi-brightness-high', 'consistent', 100);

-- Impact (Clones & Aha!)
INSERT IGNORE INTO badges (name, description, icon, criteria_type, criteria_value) VALUES 
('Growing Tree', 'Your learning paths have been cloned 25 or more times.', 'bi-tree-fill', 'clones', 25),
('Mighty Oak', 'Your breakthrough moments have inspired 50 or more learners.', 'bi-stars', 'aha_votes_received', 50);

-- Diligence (Full Logs)
INSERT IGNORE INTO badges (name, description, icon, criteria_type, criteria_value) VALUES 
('Persistent Beaver', 'Authored full daily logs for every step in 5 completed journeys.', 'bi-journal-check', 'diligent', 5),
('Master Architect', 'Authored full daily logs for every step in 10 completed journeys.', 'bi-building', 'diligent', 10);

-- Completion (Journeys)
INSERT IGNORE INTO badges (name, description, icon, criteria_type, criteria_value) VALUES 
('Iron Mountain', 'Successfully completed 5 full learning journeys!', 'bi-mountains', 'journeys_completed', 5),
('Gold Summit', 'Successfully completed 10 full learning journeys!', 'bi-trophy', 'journeys_completed', 10);

-- Activity (Participation)
INSERT IGNORE INTO badges (name, description, icon, criteria_type, criteria_value) VALUES 
('Wise Owl', 'Provided 50+ helpful comments or Aha! inspirations to fellow learners.', 'bi-lightbulb', 'community_helper', 50),
('Lion Heart', 'Provided 100+ helpful comments or Aha! inspirations to fellow learners.', 'bi-heart-fill', 'community_helper', 100);
