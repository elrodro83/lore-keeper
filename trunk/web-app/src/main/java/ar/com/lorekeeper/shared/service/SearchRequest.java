package ar.com.lorekeeper.shared.service;

import java.util.List;

import ar.com.lorekeeper.server.locator.SpringServiceLocator;
import ar.com.lorekeeper.server.service.SearchService;
import ar.com.lorekeeper.shared.bean.SearchResultProxy;

import com.google.web.bindery.requestfactory.shared.Request;
import com.google.web.bindery.requestfactory.shared.RequestContext;
import com.google.web.bindery.requestfactory.shared.Service;

@Service(value = SearchService.class, locator = SpringServiceLocator.class)
public interface SearchRequest extends RequestContext {

	Request<List<SearchResultProxy>> search(String query);
}
