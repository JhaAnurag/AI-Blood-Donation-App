class Donor:
    def __init__(self, name, blood_type, age, contact, location):
        self.name = name
        self.blood_type = blood_type.upper()
        self.age = age
        self.contact = contact
        self.location = location

    def __str__(self):
        return (f"Name: {self.name}, Blood Type: {self.blood_type}, Age: {self.age}, "
                f"Contact: {self.contact}, Location: {self.location}")

class BloodDonationSystem:
    def __init__(self):
        # Dictionary mapping blood types to lists of donors.
        self.donors_by_type = {"A": [], "B": [], "AB": [], "O": []}

    def register_donor(self):
        print("\n--- Register as Donor ---")
        name = input("Enter donor name: ")
        blood_type = input("Enter blood type (A, B, AB, O): ").upper()
        age = input("Enter donor age: ")
        contact = input("Enter donor contact: ")
        location = input("Enter donor location: ")
        donor = Donor(name, blood_type, age, contact, location)
        if blood_type in self.donors_by_type:
            self.donors_by_type[blood_type].append(donor)
        else:
            self.donors_by_type[blood_type] = [donor]
        print("Donor registered successfully!\n")

    def request_donation(self):
        print("\n--- Request Blood Donation ---")
        required_type = input("Enter required blood type (A, B, AB, O): ").upper()
        donors = self.donors_by_type.get(required_type, [])
        if donors:
            print(f"\nFound donors with blood type {required_type}:")
            for donor in donors:
                print(f"{donor.name} (Contact: {donor.contact}, Location: {donor.location})")
        else:
            print(f"\nNo donor found with blood type {required_type}.\n")
        print("")  # Extra newline for spacing

    def list_donors(self):
        print("\n--- List of Registered Donors ---")
        found_any = False
        for donors in self.donors_by_type.values():
            for donor in donors:
                print(donor)
                found_any = True
        if not found_any:
            print("No donors registered yet.\n")
        else:
            print("")

    def run(self):
        while True:
            print("=== Blood Donation System CLI ===")
            print("1. Register as Donor")
            print("2. Request Blood Donation")
            print("3. List All Donors")
            print("4. Exit")
            choice = input("Enter your choice (1-4): ")

            if choice == "1":
                self.register_donor()
            elif choice == "2":
                self.request_donation()
            elif choice == "3":
                self.list_donors()
            elif choice == "4":
                print("Exiting system. Goodbye!")
                break
            else:
                print("Invalid choice, please try again.\n")

if __name__ == "__main__":
    system = BloodDonationSystem()
    system.run()
