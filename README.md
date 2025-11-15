# Help Chat Block

A Moodle block plugin that provides a chat interface with a text area and submit button that sends messages to an AI/LLM system for processing.

## Features

- Simple text area for entering messages
- Submit button to send messages to an AI system
- Displays AI responses directly in the block
- Configurable AI backend (Core AI Subsystem, Local AI Manager, Tool AI Manager)
- Customizable system prompt for contextual AI responses
- Works on course pages, dashboard, site pages, and **question editing pages**
- **Smart context detection**: Automatically uses question editing prompt when on question editing pages

## Installation

1. Copy the `helpchat` folder to your Moodle `blocks` directory
2. Go to Site Administration > Notifications to install the plugin
3. Add the block to a page through the "Add a block" dropdown

## Configuration

### Global Configuration
1. Go to Site Administration > Plugins > Blocks > Help Chat
2. Select the AI backend you want to use
3. Set a default system prompt that will be prepended to all user messages

### Instance Configuration
1. Click the gear icon on any Help Chat block
2. Select "Configure Help Chat block"
3. Set a custom system prompt for that specific block instance

## Usage

### General Usage
1. Add the Help Chat block to any page
2. Type your message in the text area
3. Click the "Send Message" button to submit
4. View the AI response below the form

### Question Editing
When the block is added to question editing pages (such as `/question/edit.php` or question type editing pages):

1. The block will display a context indicator showing "Question Editing Mode"
2. Messages are automatically sent with a question editing focused prompt
3. The AI will provide feedback on question quality, clarity, educational value, and alignment with learning objectives

### Example Question Editing Prompts
- "Review this multiple choice question for clarity and educational value"
- "Suggest improvements for this essay question"
- "Check if this question aligns with Bloom's taxonomy level"
- "Provide feedback on the difficulty level of this question"

The system prompt (configured globally or per instance) will be prepended to your message before sending to the AI, allowing you to provide context or instructions.

## Requirements

- Moodle 4.0 or higher
- An AI backend (Core AI Subsystem, Local AI Manager, or Tool AI Manager)

## Supported Page Types

- Site pages (`site-index`)
- Course pages (`course-view`)
- Dashboard (`my`)
- General pages (`page`)
- Question editing pages (`question-*`, `admin-*`)

## License

This plugin is licensed under the GNU General Public License v3 or later.
