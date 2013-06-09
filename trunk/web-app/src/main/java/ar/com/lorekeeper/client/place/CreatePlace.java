package ar.com.lorekeeper.client.place;

import ar.com.lorekeeper.client.LoreKeeperState;
import ar.com.lorekeeper.shared.bean.PlaceProxy;
import ar.com.lorekeeper.shared.service.PlaceRequest;

import com.google.gwt.core.client.GWT;
import com.google.gwt.event.dom.client.ClickEvent;
import com.google.gwt.uibinder.client.UiBinder;
import com.google.gwt.uibinder.client.UiField;
import com.google.gwt.uibinder.client.UiHandler;
import com.google.gwt.user.client.ui.Button;
import com.google.gwt.user.client.ui.Composite;
import com.google.gwt.user.client.ui.TextArea;
import com.google.gwt.user.client.ui.TextBox;
import com.google.gwt.user.client.ui.Widget;
import com.google.web.bindery.requestfactory.shared.Receiver;

public class CreatePlace extends Composite {

	public static interface CreatePlaceHandler {
		void onCreate(PlaceProxy place);

		void onCancel();
	}

	interface CreatePlaceUiBinder extends UiBinder<Widget, CreatePlace> {
	}

	private static CreatePlaceUiBinder uiBinder = GWT
			.create(CreatePlaceUiBinder.class);

	@UiField
	TextBox txtName;

	@UiField
	TextArea txtDesc;

	@UiField
	PlaceSelector parentSelector;

	@UiField
	Button btnCreate;
	@UiField
	Button btnCancel;

	private PlaceProxy place;

	private CreatePlaceHandler handler;

	public CreatePlace() {
		initWidget(uiBinder.createAndBindUi(this));
	}

	public void setHandler(final CreatePlaceHandler handler) {
		this.handler = handler;
	}

	public PlaceProxy getPlace() {
		return place;
	}

	public void setParent(final PlaceProxy parent) {
		this.parentSelector.setSelectedPlace(parent);
	}

	@UiHandler("btnCreate")
	public void onCreateClick(final ClickEvent event) {
		final PlaceRequest placeRequest = LoreKeeperState.INSTANCE.rf
				.placeRequest();
		this.place = placeRequest.create(PlaceProxy.class);
		this.place.setName(txtName.getValue());
		this.place.setDescription(txtDesc.getValue());
		this.place.setParent(parentSelector.getSelectedPlace());
		placeRequest.persist(place).fire(new Receiver<Void>() {
			@Override
			public void onSuccess(final Void response) {
				handler.onCreate(CreatePlace.this.place);
			};
		});
	}

	@UiHandler("btnCancel")
	public void onCancleClick(final ClickEvent event) {
		handler.onCancel();
	}
}
