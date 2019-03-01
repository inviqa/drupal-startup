## Git workflow

## Branching model
| Branch name | Purpose |  
| --- | --- |  
| `production` | Signifies production ready code. All changes that are made on this branch have already undergone review and testing. |  
| `staging` | Will mostly be used as a “pointer” to snapshot a specific state on `develop` that is ready to be tested before deploying to production. In instances when ongoing development is happening alongside release QA/UAT that requires bug fixes, this branch will diverge from `develop`. |  
| `develop` | Ongoing development will happen here. |  
| `hotfix/[NAME]` | Created from master and merged back into master and also `develop`. |
| `feature/[NAME]` `bug/[NAME]` | Created from develop or release and undergo code reviews before merging back. The prefix is defined by the relevant Jira ticket and is not limited to feature and bug but these are likely the most common. |


## Workflow example
```
production   O ----- O ----------- O ----------- O	
             | \   /  \                         /
hotfix        \  O     \                       /
               \        \                     /
develop	        O ------ O ----- O --------- ◉
                 \              /        [staging]
feature           O --- O --- O
```
