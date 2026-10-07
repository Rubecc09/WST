<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LegacyCustomerSeeder extends Seeder
{
    public function run()
    {
        $customers = [
            ['EC-2024-0001', 'John Smith', '123 Main Street, Downtown', '555-0101', 'john.smith@email.com', 'MTR-001', 'residential', 'active'],
            ['EC-2024-0002', 'Sarah Johnson', '456 Oak Avenue, Suburb', '555-0102', 'sarah.j@email.com', 'MTR-002', 'residential', 'active'],
            ['EC-2024-0003', 'ABC Corporation', '789 Business Blvd, City Center', '555-0103', 'contact@abc.com', 'MTR-003', 'commercial', 'active'],
            ['EC-2024-0004', 'Michael Brown', '321 Pine Road, Eastside', '555-0104', 'mbrown@email.com', 'MTR-004', 'residential', 'active'],
            ['EC-2024-0005', 'Tech Industries Inc', '555 Industrial Park, Zone A', '555-0105', 'info@techindustries.com', 'MTR-005', 'industrial', 'active'],
            ['EC-2024-0006', 'Emily Davis', '678 Maple Drive, Westside', '555-0106', 'emily.d@email.com', 'MTR-006', 'residential', 'active'],
            ['EC-2024-0007', 'Green Mart Store', '890 Commerce Street, Plaza', '555-0107', 'greenmart@email.com', 'MTR-007', 'commercial', 'active'],
            ['EC-2024-0008', 'Robert Wilson', '234 Cedar Lane, Northside', '555-0108', 'rwilson@email.com', 'MTR-008', 'residential', 'inactive'],
            ['EC-2024-0009', 'Manufacturing Co', '432 Factory Road, Industrial Zone', '555-0109', 'info@mfgco.com', 'MTR-009', 'industrial', 'active'],
            ['EC-2024-0010', 'Lisa Anderson', '567 Birch Street, Southside', '555-0110', 'landerson@email.com', 'MTR-010', 'residential', 'active'],
            ['EC-2024-0011', 'David Martinez', '890 Elm Avenue, Central', '555-0111', 'dmartinez@email.com', 'MTR-011', 'residential', 'active'],
            ['EC-2024-0012', 'Retail Plaza LLC', '123 Shopping Center, Mall District', '555-0112', 'contact@retailplaza.com', 'MTR-012', 'commercial', 'active'],
            ['EC-2024-0013', 'Jennifer Taylor', '456 Spruce Road, Hillside', '555-0113', 'jtaylor@email.com', 'MTR-013', 'residential', 'active'],
            ['EC-2024-0014', 'Heavy Industries Ltd', '789 Manufacturing Ave, Zone B', '555-0114', 'info@heavyind.com', 'MTR-014', 'industrial', 'suspended'],
            ['EC-2024-0015', 'Thomas White', '321 Willow Lane, Riverside', '555-0115', 'twhite@email.com', 'MTR-015', 'residential', 'active'],
            ['EC-2024-0016', 'Office Complex Inc', '654 Corporate Drive, Business Park', '555-0116', 'admin@officecomplex.com', 'MTR-016', 'commercial', 'active'],
            ['EC-2024-0017', 'Patricia Harris', '987 Ash Street, Lakeside', '555-0117', 'pharris@email.com', 'MTR-017', 'residential', 'active'],
            ['EC-2024-0018', 'Auto Manufacturing', '246 Assembly Line Rd, Industrial Park', '555-0118', 'contact@automfg.com', 'MTR-018', 'industrial', 'active'],
            ['EC-2024-0019', 'Christopher Lee', '135 Poplar Avenue, Garden District', '555-0119', 'clee@email.com', 'MTR-019', 'residential', 'active'],
            ['EC-2024-0020', 'Shopping Center Co', '468 Retail Blvd, Downtown', '555-0120', 'info@shopcenter.com', 'MTR-020', 'commercial', 'active'],
            ['EC-2024-0021', 'Nancy Clark', '579 Hickory Drive, Parkside', '555-0121', 'nclark@email.com', 'MTR-021', 'residential', 'active'],
            ['EC-2024-0022', 'Steel Works Inc', '802 Foundry Road, Industrial Zone C', '555-0122', 'contact@steelworks.com', 'MTR-022', 'industrial', 'active'],
            ['EC-2024-0023', 'Daniel Lewis', '913 Sycamore Lane, Meadow View', '555-0123', 'dlewis@email.com', 'MTR-023', 'residential', 'active'],
            ['EC-2024-0024', 'Restaurant Group LLC', '246 Dining Street, Food District', '555-0124', 'info@restgroup.com', 'MTR-024', 'commercial', 'active'],
            ['EC-2024-0025', 'Karen Walker', '357 Magnolia Road, Sunset Hills', '555-0125', 'kwalker@email.com', 'MTR-025', 'residential', 'active'],
        ];

        $table = $this->db->table('customer_accounts');
        $now = '2025-10-22 01:01:51';

        foreach ($customers as [$account, $name, $address, $phone, $email, $meter, $type, $status]) {
            if ($table->where('account_number', $account)->countAllResults() > 0) {
                continue;
            }

            $table->insert([
                'account_number'  => $account,
                'customer_name'   => $name,
                'address'         => $address,
                'phone'           => $phone,
                'email'           => $email,
                'meter_number'    => $meter,
                'connection_type' => $type,
                'status'          => $status,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        }
    }
}
