INSERT INTO `useragents` (`name`, `engine`, `version`, `active`, `current`, `popular`, `gbs`, `beta`, `mobile`) VALUES

# Desktop Browsers

('Chrome', 'chrome', '.*', 1, 1, 1, 1, 0, 0),

('Firefox 3.5', 'gecko', '^1.9.1[0-9.]*$', 1, 0, 0, 1, 0, 0),
('Firefox 3.6', 'gecko', '^1.9.2[0-9.]*$', 1, 0, 1, 1, 0, 0),
('Firefox 4.0', 'gecko', '^2.0.?', 1, 1, 1, 1, 0, 0),

('Internet Explorer 6', 'msie', '^6.', 1, 0, 1, 1, 0, 0),
('Internet Explorer 7', 'msie', '^7.', 1, 0, 1, 1, 0, 0),
('Internet Explorer 8', 'msie', '^8.', 1, 0, 1, 1, 0, 0),
('Internet Explorer 9', 'msie','^9.', 1, 1, 1, 1, 0, 0),

('Opera 10.6x', 'presto', '^2.6.', 1, 0, 1, 0, 0, 0),
('Opera 11.0x', 'presto', '^2.7.', 1, 1, 1, 1, 0, 0),

('Safari 4.0', 'webkit', '^531.', 1, 0, 1, 1, 0, 0),
('Safari 5.0', 'webkit', '^533.', 1, 1, 1, 1, 0, 0),

# Mobile Browsers

('Android 1.5/1.6', 'android', '^528.5', 1, 0, 0, 0, 0, 1),
('Android 2.1', 'android', '^530.17', 1, 0, 0, 0, 0, 1),
('Android 2.2', 'android', '^533.', 1, 0, 0, 0, 0, 1),
('Android 2.3', 'android', '^533.1', 1, 1, 0, 0, 0, 1),

('Blackberry 5', 'blackberry', '^5.0', 1, 0, 0, 0, 0, 1),
('Blackberry 6', 'blackberry', '^6.0', 1, 1, 0, 0, 0, 1),

('Firefox Mobile 4.0', 'fennec', '^4.0', 1, 1, 0, 0, 0, 1),

('Mobile Safari 3.1.3', 'mobilewebkit', '^528', 1, 0, 0, 0, 0, 1),
('Mobile Safari 3.2', 'mobilewebkit', '^531', 1, 0, 0, 0, 0, 1),
('Mobile Safari 4.2', 'mobilewebkit', '^533', 1, 1, 0, 0, 0, 1),

('Opera Mobile 10.0', 'operamobile', '^2.4.18$', 1, 1, 0, 0, 0, 1),

('S60 5.0', 's60', '^5.0$', 1, 1, 0, 0, 0, 1),

('webOS Browser 1.4', 'webos', '^1.4', 1, 1, 0, 0, 0, 1),

('Windows Mobile 7', 'winmo', '^7.', 1, 1, 0, 0, 0, 1);