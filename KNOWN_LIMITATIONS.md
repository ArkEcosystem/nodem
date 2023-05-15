# Nodem - Known Limitations

This document serves to highlight some of the shortcomings of Nodem in its current sate. Although Nodem is no longer in active development, these would be good challenges to tackle for those that want to pick it up.

## Core Manager (Plugin)

- When you have no forger configured and you start the manager, it will keep that state in mind. If you then configure a forger afterwards by adding a passphrase to the `delegates.json` file or running ark config:forger (on the server), the manager will not be aware and throw errors that there is no delegate configured, even though there is. The user will first have to restart the core-manager process for it to pick up on the configured delegate.

## Nodem (Application)

- **Bulk actions**: currently it's not possible to perform bulk actions, such as updating multiple servers or to restart a group of servers.
- **Manual adjustments for updates**: there are no update scripts available to handle additional adjustments apart from `ark update` to update to a newer version of core. This means that if any manual interaction is necessary for an update, you will still require to access the server to perform these adjustments.
- **.env interaction**: it would be interesting to be able to set `.env` variables from within the Nodem UI to apply them on the server (including restarting Core afterwards to apply them)
- **Restart policies**: it is currently not possible to determine restart policies through Nodem. When an update is initiated, the server will restart core after the update completes to have it apply the latest changes, while you may want to wait until a later time for the actual restart.
- **Delegate configuration**: it is currently not possible to configure a delegate through the Nodem UI. A delegate needs to be setup on the server in advance, after which Nodem will be able to start/stop/restart the processes (including BIP38 if requested), but it is not possible to perform the configuration itself through Nodem. This means it is also not possible to remove it after one was added on the server.
- **Total resources**:  Server statistics are shown in terms of utilization in percentages, but there is no overview of the available resources in absolute amounts (e.g. server A has 8GB RAM, 256GB SSD, 4 CPU)
- **Plugin management**: it is not possible to install or remove core plugins through the Nodem UI
- **Server management**: it is not possible to perform server maintenance tasks through Nodem, such as restarting the server itself or updating packages on it.
- **Core reinstall**: it is not possible to reinstall core on a server through Nodem, this is due to the fact that the core-manager plugin relies on core.
- **Snapshot management**: it is not possible to create snapshots, restore from a snapshot, or to trigger a rollback from within Nodem.
- **Log streaming**: logs are not directly streamed from the server, so looking at logs will have a delay until it polls for new values
- **3rd party notifications**: currently there are no outside notification options, meaning that you have to keep Nodem open to see toasts when issues arise. More details on this in the `Notifications` section below.

## Notifications

At some point we'd benefit from having proper notifications in Nodem, so it can warn users of issues while they are away. This would require:

- A notifications page that lists past notifications that occured when the user was away
- A settings page that contains notification options, such as slack, discord, pushover, etc
- A settings option where a user can define which kind of notifications they want to receive and how (e.g. server behind = slack, core update available = only in Nodem imtself)
- An implementation to work with the various options to deliver notifications to the user

The notifications could for example include:

- Server disk usage (e.g. 50% used, 75%, 90%, 99%) to warn users in advance of their disk filling up
- RAM usage (90% used, spikes, swap being utilized above a given threshold)
- CPU usage spikes or heavy utilization
- Server is behind the network height
- Server has a process that errored
- Server has become unresponsive (or core manager)
- An initiated action has failed (e.g. restart/update)
- ...

## Permissions
- It is not possible to add server specific permissions enabling the nodem owner to customise which servers team member(s) can access.
