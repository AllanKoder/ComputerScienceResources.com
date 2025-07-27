
# Project Roadmap

This document outlines the planned phases and major milestones for ComputerScienceResources.com. It is intended for contributors and anyone interested in the long-term vision of the project.

---

## Phase 0: Planning (Completed January 2025)
**Goal:** Establish a solid foundation for the application.
- Define requirements and specifications
- Create paper UI designs
- Set PHP conventions and development environment
- Draft initial documentation and README

## Phase 1: Core Application Features (February–March 2025)
**Goal:** Deliver the minimum viable product (MVP) with essential resource and community features.
- Resource creation with all required fields
- Detailed resource view pages
- Upvoting and downvoting for resources
- Resource reviews (one per user per resource)
  - Reviews can be upvoted, commented on, edited, and affect resource averages
- Comment system supporting nested, paginated comments on any commentable model
- Voting system for resources and comments (upvote, downvote, remove vote)
- Resource posting rules and moderation (see Rules folder)
- Frontend designed for clarity and usability

**Target Completion:** Early March 2025

## Phase 2: Resource Editing & Community Review (April 2025)
**Goal:** Enable collaborative editing and transparent review of resource information.
- Users can propose edits to resources
- View and paginate all pending edits
- Stale edits are automatically removed after 4 months
- View, comment on, and compare proposed changes with originals
- Public approval/disapproval system for edits, with clear voting rules
- Edit creators can merge changes after sufficient community approval
- Archived and deleted edits are properly managed

**Target Completion:** End of April 2025

## Phase 3: Dynamic Tag Search (May 2025)
**Goal:** Improve discoverability through advanced tag management.
- Search and autofill tags in the frontend
- Tags sorted by frequency of use
- Tag usage counts increase as resources are created

**Target Completion:** Mid-May 2025

## Phase 4: Resource Filtering (Late May 2025)
**Goal:** Allow users to filter and find resources efficiently.
- Filter resources by any column (name, description, platforms, difficulty, pricing, tags, upvotes, review scores, etc.)

**Target Completion:** End of May 2025

## Phase 5: User Profiles & Permissions (June 2025)
**Goal:** Implement user management and permissions.
- User profile creation and management
- Email verification and secure authentication (GitHub, Google)
- Activity logging and user history
- User deletion with appropriate data retention or removal
- Profile editing (email, username, etc.)
- Permission controls to ensure users can only edit their own data

**Target Completion:** Mid-June 2025

## Phase 6: Backend Refactor & Security (July 2025)
**Goal:** Refine backend architecture and improve security.
- Refactor models for better maintainability (fillable/guarded)
- Cascade deletes and handle polymorphic relationships
- Integrate admin panel (Filament)
- Improve logging and monitoring
- Address unfinished requirements and outstanding issues

**Target Completion:** Early July 2025

## Phase 7: UI/UX Enhancements (July–August 2025)
**Goal:** Polish the user interface and experience.
- Add About page and toast notifications
- Implement a user-friendly search bar with query parameter support
- Ensure robust testing and logging
- Integrate anti-spam and security features

**Target Completion:** Early August 2025

## Phase 8: Hosting, Deployment & Documentation (August–September 2025)
**Goal:** Prepare for production and public release.
- Finalize README, license, and contributing guide
- Set up backups, analytics, and monitoring
- Deploy to VPS with Docker
- Integrate analytics and backup solutions

**Target Completion:** Early September 2025

## Phase 9: Community & Feature Expansion (Ongoing)
**Goal:** Foster community engagement and expand features based on feedback.
- Add Discussions, Bugs, and Feature Request sections
- Enhance moderation and quality control
- Add learning paths, resource lists, and favoriting
- Support for alternative/similar resources, prerequisites, and certifications

## Phase 10: Future Growth & Innovation
**Goal:** Explore new opportunities and expand the platform.
- Consider new domains (e.g. newsletters)
- Integrate new features and community-driven ideas
- Continue to improve based on user feedback and needs

---

This roadmap is subject to change based on community feedback and evolving project needs. For the latest updates, check this file or open a discussion in the repository.
