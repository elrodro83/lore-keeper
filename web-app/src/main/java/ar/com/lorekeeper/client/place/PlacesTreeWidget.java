package ar.com.lorekeeper.client.place;

import java.util.List;

import ar.com.lorekeeper.shared.bean.PlaceProxy;

import com.google.gwt.core.client.GWT;
import com.google.gwt.uibinder.client.UiBinder;
import com.google.gwt.uibinder.client.UiField;
import com.google.gwt.user.client.ui.Composite;
import com.google.gwt.user.client.ui.Label;
import com.google.gwt.user.client.ui.Widget;

public class PlacesTreeWidget extends Composite {

	interface PlacesTreeWidgetUiBinder extends
			UiBinder<Widget, PlacesTreeWidget> {
	}

	private static PlacesTreeWidgetUiBinder uiBinder = GWT
			.create(PlacesTreeWidgetUiBinder.class);

	@UiField
	Label label;

	public PlacesTreeWidget() {
		initWidget(uiBinder.createAndBindUi(this));
	}

	public void setPlaces(final List<PlaceProxy> places) {
		label.setText("" + places.size());
	}
}
