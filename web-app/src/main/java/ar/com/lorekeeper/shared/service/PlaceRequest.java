package ar.com.lorekeeper.shared.service;

import java.util.List;

import ar.com.lorekeeper.server.service.PlaceService;
import ar.com.lorekeeper.shared.bean.PlaceProxy;

import com.google.web.bindery.requestfactory.shared.Request;
import com.google.web.bindery.requestfactory.shared.RequestContext;
import com.google.web.bindery.requestfactory.shared.Service;

@Service(PlaceService.class)
public interface PlaceRequest extends RequestContext {

	Request<List<PlaceProxy>> findAllPlaces();

	Request<Void> persist(PlaceProxy place);

	Request<Void> remove(PlaceProxy place);
}
