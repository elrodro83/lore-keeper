package ar.com.lorekeeper.client;

import ar.com.lorekeeper.shared.service.LoreKeeperRequestFactory;

import com.google.gwt.user.client.ui.DeckPanel;

public class LoreKeeperState {

	public static LoreKeeperState INSTANCE = new LoreKeeperState();

	public LoreKeeperRequestFactory rf;

	public DeckPanel appPanel;

	private LoreKeeperState() {
		// nothing to do
	}
}
