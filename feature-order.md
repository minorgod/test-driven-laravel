# What should we build first?

- invite promoters
- creating accounts
- logging in as a promoter
- adding concerts
- editing concerts
- publishing concerts - be able to set start/end times for when they are available for purchase
- integrating with stripe connect to do direct payouts
- purchasing tickets

## Work backwards

Our goal is is to allow purchasing tickets. We want to eliminate everything else right now that we do not need to achieve that minimum goal. 

- Is there a way to to allow direct payout to promoters without writing any code? Yes and admin could reconcile with Stripe and cut checks to promoters on a weekly basis - so we can skip this
- Is there a way to allow publish/unpublish w/o creating UI for promoters to edit concerts? Yes, they could just send an email and we could tweak a flag in the db directly via whatever method we prefer. So there's no need for the add/edit feature right now. 
- Since we've eliminated the need to allow editing concerts, we can eliminate all the other promoter functionality. 

So, we can focus only on the "purchasing tickets" feature right now. 

# What should we test first

- Purchasing tickets
  - View the concert listing
    - Allow people to view published concerts
    - Not allow people to view unpublished concerts
  - Pay for the tickets
  - View their purchased tickets in the browser
  - Send an email confirmation with a link back to the tickets

