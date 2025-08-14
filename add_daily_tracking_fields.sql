-- Add daily tracking fields to business_savings table
ALTER TABLE business_savings 
ADD COLUMN daily_collections_count INT DEFAULT 0 AFTER last_collection_date,
ADD COLUMN daily_collected_amount DECIMAL(15,2) DEFAULT 0 AFTER daily_collections_count; 